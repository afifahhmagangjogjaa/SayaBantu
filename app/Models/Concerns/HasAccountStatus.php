<?php

namespace App\Models\Concerns;

/**
 * Status akun, role, dan penanganan sesi (ban/shadow ban).
 * Diekstrak dari App\Models\User agar model tidak terlalu besar.
 */
trait HasAccountStatus
{
    public function isBanned(): bool
    {
        return (bool) ($this->is_banned ?? false) || $this->status === 'blocked';
    }

    public function purgeSessions(): void
    {
        try {
            // 1. Kosongkan remember_token di database agar auto-login / remember me di browser mati total
            if ($this->remember_token !== null) {
                $this->forceFill(['remember_token' => null])->saveQuietly();
            }

            // 2. Hapus seluruh data sesi di tabel database sessions
            if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $this->id)->delete();
            }

            // 3. Hapus file sesi fisik jika menggunakan file driver
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                $files = @glob($sessionPath . '/*');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if (is_file($file) && basename($file) !== '.gitignore') {
                            $content = @file_get_contents($file);
                            if ($content !== false && strpos($content, (string) $this->id) !== false) {
                                if (preg_match('/login_[a-z0-9_]+[^\d]+' . $this->id . '([^\d]|$)/', $content)) {
                                    @unlink($file);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal membersihkan sesi user #{$this->id}: " . $e->getMessage());
        }
    }

    public function isShadowBanned(): bool
    {
        return (bool) ($this->is_shadow_banned ?? false);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKustomer()
    {
        return $this->isCustomer();
    }

    public function isMitra()
    {
        return $this->role === 'mitra';
    }

    public function isCustomer()
    {
        // Accept both 'customer' (new) and 'kustomer' (legacy) values
        return in_array($this->role, ['customer', 'kustomer']);
    }

    public function getRoleEnumAttribute(): ?\App\Enums\UserRole
    {
        $roleValue = ($this->role === 'kustomer') ? 'customer' : (string) $this->role;
        return \App\Enums\UserRole::tryFrom($roleValue);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role_enum?->label() ?? ucfirst((string) $this->role);
    }

    public function isVerified()
    {
        return $this->verified;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
