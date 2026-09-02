<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
use App\Services\LocationTrackingService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class GpsSimulator extends Component
{
    public $helpId;
    public $isSimulating = false;
    public $currentLat;
    public $currentLng;
    public $targetLat;
    public $targetLng;
    public $stepSize = 5; // meter per step (ubah dari 20 ke 5 untuk lebih realistis)
    public $simulationSpeed = 5; // detik per update (ubah dari 2 ke 5)

    protected $locationService;

    public function boot(LocationTrackingService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function mount($helpId)
    {
        $this->helpId = $helpId;
        $help = Help::find($helpId);
        
        if (!$help) {
            return;
        }
        
        // Set target sebagai lokasi customer (fallback to city or default if empty)
        $this->targetLat = !empty($help->latitude) ? floatval($help->latitude) : (!empty($help->city?->latitude) ? floatval($help->city->latitude) : -7.7956);
        $this->targetLng = !empty($help->longitude) ? floatval($help->longitude) : (!empty($help->city?->longitude) ? floatval($help->city->longitude) : 110.3695);
        
        // Jika sudah ada partner location di database dan jarak > 20m, gunakan itu
        if ($help->partner_current_lat && $help->partner_current_lng && $this->calculateDistanceSimple(floatval($help->partner_current_lat), floatval($help->partner_current_lng), $this->targetLat, $this->targetLng) > 20) {
            $this->currentLat = floatval($help->partner_current_lat);
            $this->currentLng = floatval($help->partner_current_lng);
        } else {
            // Generate random location 600-1000m dari target
            $this->generateRandomStartLocation();
        }
        
        Log::info('GPS Simulator Mounted', [
            'help_id' => $this->helpId,
            'current' => [$this->currentLat, $this->currentLng],
            'target' => [$this->targetLat, $this->targetLng],
            'distance' => $this->calculateDistanceSimple($this->currentLat, $this->currentLng, $this->targetLat, $this->targetLng)
        ]);
    }
    
    public function calculateDistanceSimple($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000;
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function generateRandomStartLocation()
    {
        if (!$this->targetLat || !$this->targetLng) {
            $this->targetLat = -7.7956;
            $this->targetLng = 110.3695;
        }
        
        // Generate random point 600-1000 meter dari target
        $distance = rand(600, 1000); // meter
        $angle = rand(0, 360); // degree
        
        $earthRadius = 6371000; // meter
        $angularDistance = $distance / $earthRadius;
        $bearing = deg2rad($angle);
        
        $lat1 = deg2rad($this->targetLat);
        $lng1 = deg2rad($this->targetLng);
        
        $lat2 = asin(sin($lat1) * cos($angularDistance) + 
                     cos($lat1) * sin($angularDistance) * cos($bearing));
        
        $lng2 = $lng1 + atan2(sin($bearing) * sin($angularDistance) * cos($lat1),
                              cos($angularDistance) - sin($lat1) * sin($lat2));
        
        $this->currentLat = rad2deg($lat2);
        $this->currentLng = rad2deg($lng2);
        
        Log::info('Random start location generated', [
            'target' => [$this->targetLat, $this->targetLng],
            'start' => [$this->currentLat, $this->currentLng],
            'distance_meters' => $distance,
            'bearing_degrees' => $angle
        ]);
    }

    public function startSimulation()
    {
        $help = Help::find($this->helpId);
        
        if (!$help) {
            Log::error('GPS Simulator: Help not found', ['help_id' => $this->helpId]);
            return;
        }

        // Pastikan target location terisi
        $this->targetLat = !empty($help->latitude) ? floatval($help->latitude) : (!empty($help->city?->latitude) ? floatval($help->city->latitude) : -7.7956);
        $this->targetLng = !empty($help->longitude) ? floatval($help->longitude) : (!empty($help->city?->longitude) ? floatval($help->city->longitude) : 110.3695);

        // Jika jarak ke target < 25 meter, buat titik awal baru 600-1000 meter
        $dist = $this->calculateDistanceSimple($this->currentLat, $this->currentLng, $this->targetLat, $this->targetLng);
        if ($dist < 25) {
            $this->generateRandomStartLocation();
        }

        // Set lokasi awal di database dan ubah status ke partner_on_the_way
        $this->locationService->setInitialLocation($help, $this->currentLat, $this->currentLng);
        $help->update([
            'status' => 'partner_on_the_way',
            'partner_started_moving_at' => now(),
            'partner_arrived_at' => null,
            'partner_current_lat' => $this->currentLat,
            'partner_current_lng' => $this->currentLng,
        ]);

        $this->isSimulating = true;
        $this->dispatch('simulation-started');
        $this->dispatch('status-changed', [
            'helpId' => $this->helpId,
            'oldStatus' => $help->status,
            'newStatus' => 'partner_on_the_way'
        ]);
        
        Log::info('GPS Simulator: Simulation started', [
            'help_id' => $this->helpId,
            'start_location' => [$this->currentLat, $this->currentLng],
            'target_location' => [$this->targetLat, $this->targetLng],
            'distance' => $this->calculateDistanceSimple($this->currentLat, $this->currentLng, $this->targetLat, $this->targetLng)
        ]);
    }

    public function stopSimulation()
    {
        $this->isSimulating = false;
        $this->dispatch('simulation-stopped');
        
        Log::info('GPS Simulator: Simulation stopped', ['help_id' => $this->helpId]);
    }

    public function simulateStep()
    {
        if (!$this->isSimulating) {
            return ['still_moving' => false, 'message' => 'Simulation not running'];
        }

        $lat1 = deg2rad($this->currentLat);
        $lng1 = deg2rad($this->currentLng);
        $lat2 = deg2rad($this->targetLat);
        $lng2 = deg2rad($this->targetLng);
        
        // Bearing (arah) ke target
        $y = sin($lng2 - $lng1) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lng2 - $lng1);
        $bearing = atan2($y, $x);
        
        // Jarak ke target
        $earthRadius = 6371000;
        $latDiff = $lat2 - $lat1;
        $lngDiff = $lng2 - $lng1;
        $a = sin($latDiff / 2) * sin($latDiff / 2) + 
             cos($lat1) * cos($lat2) * sin($lngDiff / 2) * sin($lngDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceToTarget = $earthRadius * $c;

        $isArrived = false;

        // Jika sudah dekat (<= stepSize atau <= 20 meter), tiba di lokasi!
        if ($distanceToTarget <= max($this->stepSize, 20)) {
            $this->currentLat = $this->targetLat;
            $this->currentLng = $this->targetLng;
            $this->stopSimulation();
            $isArrived = true;
        } else {
            // Gerak selangkah ke arah target
            $angularDistance = $this->stepSize / $earthRadius;
            
            $newLat = asin(sin($lat1) * cos($angularDistance) + 
                          cos($lat1) * sin($angularDistance) * cos($bearing));
            
            $newLng = $lng1 + atan2(sin($bearing) * sin($angularDistance) * cos($lat1),
                                   cos($angularDistance) - sin($lat1) * sin($newLat));
            
            $this->currentLat = rad2deg($newLat);
            $this->currentLng = rad2deg($newLng);
        }

        // Update lokasi ke database
        $help = Help::find($this->helpId);
        if ($help) {
            $result = $this->locationService->updatePartnerLocation(
                $help, 
                $this->currentLat, 
                $this->currentLng
            );
            
            if ($isArrived) {
                $help->update([
                    'status' => 'partner_arrived',
                    'partner_arrived_at' => now(),
                    'partner_current_lat' => $this->currentLat,
                    'partner_current_lng' => $this->currentLng,
                ]);

                $this->dispatch('status-changed', [
                    'helpId' => $this->helpId,
                    'oldStatus' => $help->status,
                    'newStatus' => 'partner_arrived'
                ]);
            } elseif (isset($result['status_changed']) && $result['status_changed']) {
                $this->dispatch('status-changed', [
                    'helpId' => $this->helpId,
                    'oldStatus' => $result['old_status'] ?? null,
                    'newStatus' => $result['status'] ?? $help->status
                ]);
            }

            return [
                'distance' => round($this->calculateDistanceSimple($this->currentLat, $this->currentLng, $this->targetLat, $this->targetLng)),
                'status' => $help->status,
                'still_moving' => $this->isSimulating,
                'currentLat' => $this->currentLat,
                'currentLng' => $this->currentLng,
            ];
        }
    }

    public function setStepSize($size)
    {
        $this->stepSize = max(10, min(100, $size)); // 10-100 meter
    }

    public function setSpeed($speed)
    {
        $this->simulationSpeed = max(1, min(10, $speed)); // 1-10 detik
    }

    public function teleportToTarget()
    {
        $help = Help::find($this->helpId);
        if (!$help) return;

        $this->targetLat = !empty($help->latitude) ? floatval($help->latitude) : (!empty($help->city?->latitude) ? floatval($help->city->latitude) : -7.7956);
        $this->targetLng = !empty($help->longitude) ? floatval($help->longitude) : (!empty($help->city?->longitude) ? floatval($help->city->longitude) : 110.3695);

        $this->currentLat = $this->targetLat;
        $this->currentLng = $this->targetLng;
        
        $this->locationService->updatePartnerLocation(
            $help, 
            $this->currentLat, 
            $this->currentLng
        );
        
        $help->update([
            'status' => 'partner_arrived',
            'partner_arrived_at' => now(),
            'partner_current_lat' => $this->currentLat,
            'partner_current_lng' => $this->currentLng,
        ]);
        
        $this->stopSimulation();

        $this->dispatch('status-changed', [
            'helpId' => $this->helpId,
            'oldStatus' => $help->status,
            'newStatus' => 'partner_arrived'
        ]);
    }

    public function getDistanceToTargetProperty()
    {
        if (!$this->currentLat || !$this->currentLng || !$this->targetLat || !$this->targetLng) {
            return 0;
        }
        
        return $this->calculateDistanceSimple(
            $this->currentLat, 
            $this->currentLng, 
            $this->targetLat, 
            $this->targetLng
        );
    }

    public function render()
    {
        return view('livewire.mitra.gps-simulator');
    }
}
