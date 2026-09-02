<?php

namespace App\Services;

use App\Models\Help;
use Illuminate\Support\Facades\Log;

class LocationTrackingService
{
    /**
     * Jarak minimum untuk menganggap mitra mulai bergerak (dalam meter)
     */
    const MOVING_THRESHOLD = 20;

    /**
     * Jarak maksimum untuk menganggap mitra sudah tiba di lokasi (dalam meter)
     */
    const ARRIVAL_THRESHOLD = 20;

    /**
     * Hitung jarak antara dua titik koordinat menggunakan formula Haversine
     * 
     * @param float $lat1 Latitude titik pertama
     * @param float $lng1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lng2 Longitude titik kedua
     * @return float Jarak dalam meter
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000; // meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Update lokasi mitra dan status bantuan berdasarkan GPS
     * 
     * @param Help $help
     * @param float $currentLat
     * @param float $currentLng
     * @return array
     */
    public function updatePartnerLocation(Help $help, float $currentLat, float $currentLng): array
    {
        // Validasi apakah help masih aktif
        if (!in_array($help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way', 'partner_arrived', 'in_progress', 'sedang_diproses'])) {
            return [
                'success' => false,
                'message' => 'Bantuan tidak dalam status aktif'
            ];
        }

        // Update lokasi current
        $help->partner_current_lat = $currentLat;
        $help->partner_current_lng = $currentLng;

        // Cek apakah lokasi awal sudah disimpan
        if (!$help->partner_initial_lat || !$help->partner_initial_lng) {
            // Simpan sebagai lokasi awal
            $help->partner_initial_lat = $currentLat;
            $help->partner_initial_lng = $currentLng;
            $help->save();

            Log::info("Lokasi awal mitra disimpan", [
                'help_id' => $help->id,
                'lat' => $currentLat,
                'lng' => $currentLng
            ]);

            return [
                'success' => true,
                'message' => 'Lokasi awal mitra berhasil disimpan',
                'status' => $help->status,
                'distance_from_initial' => 0,
                'distance_to_customer' => null
            ];
        }

        // Hitung jarak dari lokasi awal
        $distanceFromInitial = $this->calculateDistance(
            $help->partner_initial_lat,
            $help->partner_initial_lng,
            $currentLat,
            $currentLng
        );

        // Hitung jarak ke lokasi customer (fallback ke koordinat kota atau default jika null/0)
        $targetLat = !empty($help->latitude) ? (float) $help->latitude : (!empty($help->city?->latitude) ? (float) $help->city->latitude : -7.7956);
        $targetLng = !empty($help->longitude) ? (float) $help->longitude : (!empty($help->city?->longitude) ? (float) $help->city->longitude : 110.3695);
        
        $distanceToCustomer = $this->calculateDistance(
            $currentLat,
            $currentLng,
            $targetLat,
            $targetLng
        );

        $statusChanged = false;
        $oldStatus = $help->status;

        // Logic update status berdasarkan jarak
        if (in_array($help->status, ['memperoleh_mitra', 'taken'])) {
            // Jika mitra bergerak lebih dari 20 meter dari titik awal
            if ($distanceFromInitial >= self::MOVING_THRESHOLD) {
                $help->status = 'partner_on_the_way';
                $help->partner_started_moving_at = now();
                $statusChanged = true;

                Log::info("Status berubah: Rekan jasa menuju lokasi", [
                    'help_id' => $help->id,
                    'old_status' => $oldStatus,
                    'distance_from_initial' => round($distanceFromInitial, 2)
                ]);
            }
        }

        // Jika mitra dalam perjalanan, cek apakah sudah sampai
        if (in_array($help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way']) && $distanceToCustomer !== null) {
            // Jika mitra sudah dekat dengan lokasi customer (dalam radius 50m)
            if ($distanceToCustomer <= self::ARRIVAL_THRESHOLD) {
                $help->status = 'partner_arrived';
                $help->partner_arrived_at = now();
                $statusChanged = true;

                Log::info("Status berubah: Rekan jasa tiba di lokasi", [
                    'help_id' => $help->id,
                    'old_status' => $oldStatus,
                    'distance_to_customer' => round($distanceToCustomer, 2)
                ]);
            }
        }

        $help->save();

        // Notify the customer via database notification when status changed
        if ($statusChanged && $help->user) {
            try {
                $help->user->notify(new \App\Notifications\HelpStatusNotification($help, $oldStatus, $help->status, $help->mitra));
                Log::info('Sent HelpStatusNotification to customer', ['help_id' => $help->id, 'old_status' => $oldStatus, 'new_status' => $help->status]);
            } catch (\Throwable $e) {
                Log::warning('Failed to send HelpStatusNotification: ' . $e->getMessage(), ['help_id' => $help->id]);
            }
        }

        return [
            'success' => true,
            'message' => 'Lokasi berhasil diupdate',
            'status' => $help->status,
            'status_changed' => $statusChanged,
            'old_status' => $oldStatus,
            'distance_from_initial' => round($distanceFromInitial, 2),
            'distance_to_customer' => $distanceToCustomer ? round($distanceToCustomer, 2) : null,
            'thresholds' => [
                'moving' => self::MOVING_THRESHOLD,
                'arrival' => self::ARRIVAL_THRESHOLD
            ]
        ];
    }

    /**
     * Set lokasi awal mitra saat mengambil bantuan
     * 
     * @param Help $help
     * @param float $lat
     * @param float $lng
     * @return void
     */
    public function setInitialLocation(Help $help, float $lat, float $lng): void
    {
        $help->partner_initial_lat = $lat;
        $help->partner_initial_lng = $lng;
        $help->partner_current_lat = $lat;
        $help->partner_current_lng = $lng;
        $help->save();

        Log::info("Lokasi awal mitra di-set saat mengambil bantuan", [
            'help_id' => $help->id,
            'lat' => $lat,
            'lng' => $lng
        ]);
    }

    /**
     * Dapatkan status tracking untuk ditampilkan ke user
     * 
     * @param Help $help
     * @return array
     */
    public function getTrackingStatus(Help $help): array
    {
        $status = [
            'help_id' => $help->id,
            'current_status' => $help->status,
            'partner_location' => null,
            'customer_location' => null,
            'distance_to_customer' => null,
            'estimated_status' => null
        ];

        if ($help->partner_current_lat && $help->partner_current_lng) {
            $status['partner_location'] = [
                'lat' => (float) $help->partner_current_lat,
                'lng' => (float) $help->partner_current_lng
            ];
        }

        if ($help->latitude && $help->longitude) {
            $status['customer_location'] = [
                'lat' => (float) $help->latitude,
                'lng' => (float) $help->longitude
            ];

            // Hitung jarak jika kedua lokasi tersedia
            if ($status['partner_location']) {
                $status['distance_to_customer'] = round(
                    $this->calculateDistance(
                        $help->partner_current_lat,
                        $help->partner_current_lng,
                        $help->latitude,
                        $help->longitude
                    ),
                    2
                );
            }
        }

        return $status;
    }
}
