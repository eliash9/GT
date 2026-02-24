<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tipe Laporan</th>
            <th>Nama Pelapor</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Lembaga Tugas</th>
            <th>PJGT Lembaga</th>
            <th>Target GT</th>
            <th>Status Laporan</th>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $category->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th>[<?php echo e($category->name); ?>] <?php echo e($question->question); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td style="text-transform: uppercase;"><?php echo e($report->report_type); ?></td>
                <td><?php echo e($report->reporter_name); ?></td>
                <td><?php echo e($report->period_month); ?></td>
                <td><?php echo e($report->period_year); ?></td>
                <td><?php echo e($report->lembaga ? $report->lembaga->nama : '-'); ?></td>
                <td><?php echo e($report->pjgt ? $report->pjgt->nama : '-'); ?></td>
                <td><?php echo e($report->santri ? $report->santri->nama : '-'); ?></td>
                <td><?php echo e(ucfirst($report->status)); ?></td>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $category->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $answerRecord = $report->answers->firstWhere('report_question_id', $question->id);
                            $answerText = $answerRecord ? $answerRecord->answer : '-';
                            
                            // Handling array type string if using json format
                            if ($question->type === 'checkbox') {
                                try {
                                    $decoded = json_decode($answerText, true);
                                    if (is_array($decoded)) {
                                        $answerText = implode(", ", $decoded);
                                    }
                                } catch(\Exception $e) {}
                            }
                        ?>
                        <td><?php echo e($answerText); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH F:\CODE\GT\gt\resources\views/exports/reports.blade.php ENDPATH**/ ?>