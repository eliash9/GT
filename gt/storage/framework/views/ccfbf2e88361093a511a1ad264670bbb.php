<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($title); ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-top: 20px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($title); ?></h2>
        <?php if($session): ?>
            <h3>Sesi: <?php echo e($session->title); ?></h3>
            <p>Tipe: <?php echo e(strtoupper($session->type)); ?> | Tanggal: <?php echo e($session->created_at->format('d F Y')); ?></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Role</th>
                <th>Lembaga</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $role = $att->user->getRoleNames()->first();
                    $lembaga = '-';
                    if ($role === 'gt') {
                        $lembaga = $att->user->santri->penugasanAktif->lembaga->nama ?? '-';
                    } elseif (in_array($role, ['pjgt', 'korwil'])) {
                        $lembaga = $att->user->pjgt->lembagas->count() . ' Lembaga';
                    }
                ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($att->user->name); ?></td>
                    <td><?php echo e(strtoupper($role)); ?></td>
                    <td><?php echo e($lembaga); ?></td>
                    <td><?php echo e($att->scanned_at->format('H:i:s')); ?></td>
                    <td><?php echo e($att->status === 'present' ? 'Hadir' : 'Telat'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        Total Kehadiran: <?php echo e($attendances->count()); ?> orang
    </div>

    <div class="footer">
        Dicetak pada: <?php echo e(now()->format('d/m/Y H:i:s')); ?> | Aplikasi PSM UGT
    </div>
</body>
</html>
<?php /**PATH F:\CODE\GT\gt\resources\views/pdf/attendance.blade.php ENDPATH**/ ?>