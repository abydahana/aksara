<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <div class="card rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-fingerprint text-primary me-2"></i><?= phrase('Passkeys & Biometrics') ?>
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#registerPasskeyModal">
                            <i class="mdi mdi-plus me-1"></i><?= phrase('Add Passkey') ?>
                        </button>
                    </div>
                    <p class="text-muted mb-0">
                        <?= phrase('Use your fingerprint, Face ID, or security key for fast and secure passwordless sign-in.') ?>
                    </p>
                </div>
                <div class="card-body px-4">
                    <?php if (! empty($passkeys)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th><?= phrase('Device / Label') ?></th>
                                        <th><?= phrase('Created') ?></th>
                                        <th><?= phrase('Last Used') ?></th>
                                        <th class="text-end"><?= phrase('Action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($passkeys as $passkey): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold">
                                                    <i class="mdi mdi-cellphone-key me-2 text-primary"></i><?= htmlspecialchars($passkey->device_name) ?>
                                                </div>
                                            </td>
                                            <td class="small text-muted">
                                                <?= $passkey->created_at ? date('d M Y H:i', strtotime($passkey->created_at)) : '-' ?>
                                            </td>
                                            <td class="small text-muted">
                                                <?= $passkey->last_used_at ? date('d M Y H:i', strtotime($passkey->last_used_at)) : phrase('Never') ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= base_url('administrative/account/passkey/delete/' . $passkey->id) ?>" class="btn btn-link text-danger btn-sm p-0 --xhr --confirm" data-confirm="<?= phrase('Are you sure you want to remove this passkey?') ?>">
                                                    <i class="mdi mdi-delete-outline me-1"></i><?= phrase('Delete') ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="mdi mdi-shield-key-outline mdi-5x text-muted opacity-50"></i>
                            <h6 class="mt-3 text-secondary"><?= phrase('No Passkeys Registered Yet') ?></h6>
                            <p class="text-muted mb-3">
                                <?= phrase('Registering a passkey allows you to sign in instantly using biometric verification.') ?>
                            </p>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerPasskeyModal">
                                <i class="mdi mdi-plus me-1"></i><?= phrase('Add Your First Passkey') ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center">
                <a href="<?= base_url('administrative/account') ?>" class="text-muted --xhr">
                    <i class="mdi mdi-arrow-left me-1"></i><?= phrase('Back to Account Settings') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Register Passkey -->
<div class="modal fade" id="registerPasskeyModal" tabindex="-1" aria-labelledby="registerPasskeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="registerPasskeyModalLabel">
                    <i class="mdi mdi-fingerprint me-2 text-primary"></i><?= phrase('Add New Passkey') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted mb-3">
                    <?= phrase('Give this passkey a friendly device name so you can recognize it later.') ?>
                </p>
                <div class="mb-3">
                    <label for="passkeyDeviceName" class="form-label fw-bold"><?= phrase('Device Name') ?></label>
                    <input type="text" class="form-control rounded-3" id="passkeyDeviceName" placeholder="<?= phrase('e.g. MacBook Touch ID, iPhone, YubiKey') ?>" value="My Passkey Device">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal"><?= phrase('Cancel') ?></button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnCreatePasskey">
                    <i class="mdi mdi-check me-1"></i><?= phrase('Continue') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset_url('local/js/passkey.min.js') ?>"></script>
<script>
    document.getElementById('btnCreatePasskey')?.addEventListener('click', function() {
        const modalEl = document.getElementById('registerPasskeyModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        Passkey.register(
            '<?= base_url('administrative/account/passkey/register') ?>',
            '<?= base_url('administrative/account/passkey/verify') ?>',
            'passkeyDeviceName'
        );
    });
</script>
