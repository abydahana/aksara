<div>
    <?php foreach ($languages as $key => $val): ?>
        <?php if ($key): ?>
            <hr />
        <?php endif; ?>
        <?php if ($val->code == get_userdata('language')): ?>
            <b class="d-block">
                <?= $val->language; ?>
            </b>
        <?php else: ?>
            <a href="<?= base_url('xhr/language/' . $val->code); ?>" class="d-block --xhr">
                <?= $val->language; ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
