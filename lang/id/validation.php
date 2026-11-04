<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi standar pesan error yang digunakan oleh
    | kelas validasi. Beberapa aturan memiliki beberapa versi seperti
    | aturan ukuran. Silakan ubah setiap pesan ini sesuai kebutuhan Anda.
    |
    */

    'accepted'             => ':Attribute harus diterima.',
    'accepted_if'          => ':Attribute harus diterima ketika :other adalah :value.',
    'active_url'           => ':Attribute bukan URL yang valid.',
    'after'                => ':Attribute harus berupa tanggal setelah :date.',
    'after_or_equal'       => ':Attribute harus berupa tanggal setelah atau sama dengan tanggal :date.',
    'alpha'                => ':Attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => ':Attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':Attribute harus berupa array.',
    'before'               => ':Attribute harus berupa tanggal sebelum :date.',
    'before_or_equal'      => ':Attribute harus berupa tanggal sebelum atau sama dengan tanggal :date.',
    'between'              => [
        'numeric' => ':Attribute harus bernilai antara :min sampai :max.',
        'file'    => ':Attribute harus berukuran antara :min sampai :max kilobita.',
        'string'  => ':Attribute harus berkarakter antara :min sampai :max karakter.',
        'array'   => ':Attribute harus memiliki :min sampai :max anggota.',
    ],
    'boolean'              => ':Attribute harus bernilai true atau false.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'current_password'     => 'Kata sandi salah.',
    'date'                 => ':Attribute bukan tanggal yang valid.',
    'date_equals'          => ':Attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          => ':Attribute tidak cocok dengan format :format.',
    'decimal'              => ':Attribute harus memiliki :decimal tempat desimal.',
    'declined'             => ':Attribute ini harus ditolak.',
    'declined_if'          => ':Attribute ini harus ditolak ketika :other bernilai :value.',
    'different'            => ':Attribute dan :other harus berbeda.',
    'digits'               => ':Attribute harus terdiri dari :digits angka.',
    'digits_between'       => ':Attribute harus terdiri dari :min sampai :max angka.',
    'dimensions'           => ':Attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => ':Attribute memiliki nilai yang duplikat.',
    'email'                => ':Attribute harus berupa alamat email yang valid.',
    'ends_with'            => ':Attribute harus diakhiri salah satu dari berikut: :values.',
    'enum'                 => ':Attribute yang dipilih tidak valid.',
    'exists'               => ':Attribute yang dipilih tidak valid.',
    'file'                 => ':Attribute harus berupa sebuah berkas.',
    'filled'               => ':Attribute harus memiliki nilai.',
    'gt'                   => [
        'numeric' => ':Attribute harus bernilai lebih besar dari :value.',
        'file'    => ':Attribute harus berukuran lebih besar dari :value kilobita.',
        'string'  => ':Attribute harus berkarakter lebih besar dari :value karakter.',
        'array'   => ':Attribute harus memiliki lebih dari :value anggota.',
    ],
    'gte'                  => [
        'numeric' => ':Attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'file'    => ':Attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'string'  => ':Attribute harus berkarakter lebih besar dari atau sama dengan :value karakter.',
        'array'   => ':Attribute harus terdiri dari :value anggota atau lebih.',
    ],
    'image'                => ':Attribute harus berupa gambar.',
    'in'                   => ':Attribute yang dipilih tidak valid.',
    'in_array'             => ':Attribute tidak ada di dalam :other.',
    'integer'              => ':Attribute harus berupa bilangan bulat.',
    'ip'                   => ':Attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':Attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':Attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':Attribute harus berupa string JSON yang valid.',
    'lowercase'            => ':Attribute harus berupa huruf kecil.',
    'lt'                   => [
        'numeric' => ':Attribute harus bernilai kurang dari :value.',
        'file'    => ':Attribute harus berukuran kurang dari :value kilobita.',
        'string'  => ':Attribute harus berkarakter kurang dari :value karakter.',
        'array'   => ':Attribute harus memiliki kurang dari :value anggota.',
    ],
    'lte'                  => [
        'numeric' => ':Attribute harus bernilai kurang dari atau sama dengan :value.',
        'file'    => ':Attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'string'  => ':Attribute harus berkarakter kurang dari atau sama dengan :value karakter.',
        'array'   => ':Attribute harus tidak lebih dari :value anggota.',
    ],
    'mac_address'          => ':Attribute harus berupa alamat MAC yang valid.',
    'max'                  => [
        'numeric' => ':Attribute maksimal bernilai :max.',
        'file'    => ':Attribute maksimal berukuran :max kilobita.',
        'string'  => ':Attribute maksimal berisi :max karakter.',
        'array'   => ':Attribute maksimal memiliki :max anggota.',
    ],
    'max_digits'           => ':Attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes'                => ':Attribute harus berupa berkas berjenis: :values.',
    'mimetypes'            => ':Attribute harus berupa berkas berjenis: :values.',
    'min'                  => [
        'numeric' => ':Attribute minimal bernilai :min.',
        'file'    => ':Attribute minimal berukuran :min kilobita.',
        'string'  => ':Attribute minimal berisi :min karakter.',
        'array'   => ':Attribute minimal memiliki :min anggota.',
    ],
    'min_digits'           => ':Attribute tidak boleh memiliki kurang dari :min digit.',
    'missing'              => ':Attribute harus hilang.',
    'missing_if'           => ':Attribute harus hilang saat :other adalah :value.',
    'missing_unless'       => ':Attribute harus hilang kecuali :other adalah :value.',
    'missing_with'         => ':Attribute harus hilang saat :values ada.',
    'missing_with_all'     => ':Attribute harus hilang saat :values ada semua.',
    'multiple_of'          => ':Attribute harus merupakan kelipatan dari :value.',
    'not_in'               => ':Attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':Attribute harus berupa angka.',
    'password'             => [
        'letters'          => ':Attribute harus mengandung setidaknya satu huruf.',
        'mixed'            => ':Attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers'          => ':Attribute harus mengandung setidaknya satu angka.',
        'symbols'          => ':Attribute harus mengandung setidaknya satu simbol.',
        'uncompromised'    => ':Attribute yang diberikan telah bocor dalam pelanggaran data. Pilih :attribute yang berbeda.',
    ],
    'present'              => ':Attribute wajib ada.',
    'prohibited'           => ':Attribute tidak boleh ada.',
    'prohibited_if'        => ':Attribute tidak boleh ada ketika :other adalah :value.',
    'prohibited_unless'    => ':Attribute tidak boleh ada kecuali :other ada di :values.',
    'prohibits'            => ':Attribute melarang :other untuk ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':Attribute wajib diisi.',
    'required_array_keys'  => ':Attribute harus berisi entri untuk: :values.',
    'required_if'          => ':Attribute wajib diisi ketika :other bernilai :value.',
    'required_if_accepted' => ':Attribute wajib diisi ketika :other diterima.',
    'required_unless'      => ':Attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => ':Attribute wajib diisi jika terdapat :values.',
    'required_with_all'    => ':Attribute wajib diisi jika terdapat :values.',
    'required_without'     => ':Attribute wajib diisi jika tidak terdapat :values.',
    'required_without_all' => ':Attribute wajib diisi jika sama sekali tidak ada :values.',
    'same'                 => ':Attribute dan :other harus sama.',
    'size'                 => [
        'numeric' => ':Attribute harus berukuran :size.',
        'file'    => ':Attribute harus berukuran :size kilobita.',
        'string'  => ':Attribute harus berkarakter :size karakter.',
        'array'   => ':Attribute harus mengandung :size anggota.',
    ],
    'starts_with'          => ':Attribute harus diawali salah satu dari berikut: :values.',
    'string'               => ':Attribute harus berupa string.',
    'timezone'             => ':Attribute harus berupa zona waktu yang valid.',
    'unique'               => ':Attribute sudah digunakan.',
    'uploaded'             => ':Attribute gagal diunggah.',
    'uppercase'            => ':Attribute harus berupa huruf besar.',
    'url'                  => 'Format :attribute tidak valid.',
    'ulid'                 => ':Attribute harus berupa ULID yang valid.',
    'uuid'                 => ':Attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Atribut Validasi
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'alamat email',
        'password' => 'kata sandi',
        'phone' => 'nomor telepon',
        'name' => 'nama lengkap',
        'otp_code' => 'kode OTP',
        'password_confirmation' => 'konfirmasi kata sandi',
        'form.email' => 'nomor HP atau email',
        'form.password' => 'kata sandi',
    ],

];

