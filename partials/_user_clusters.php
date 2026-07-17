<h4 class="my-3 fw-normal"><?= __('Clusters') ?></h4>

<?php $model = $this->formGetModel(); ?>
<?php $clusters = $model->clusters; ?>

<?php if ($clusters->isEmpty()): ?>
    <p class="text-muted"><?= __('No clusters assigned') ?></p>
<?php else: ?>
    <table class="table data-table">
        <thead>
            <tr>
                <th><?= __('Name') ?></th>
                <th><?= __('Slug') ?></th>
                <th><?= __('Plan') ?></th>
                <th><?= __('Created at') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clusters as $cluster): ?>
                <tr>
                    <td>
                        <a href="<?= Backend::url('initbiz/cumuluscore/clusters/update/' . $cluster->id) ?>">
                            <?= e($cluster->name) ?>
                        </a>
                    </td>
                    <td><?= e($cluster->slug) ?></td>
                    <td><?= e(optional($cluster->plan)->name) ?></td>
                    <td><?= e($cluster->created_at) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
