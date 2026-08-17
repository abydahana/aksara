<?php

/**
 * @var mixed $results
 * @var mixed $totalPhrases
 * @var mixed $pagination
 * @var mixed $phrases
 * @var array $phraseScopes
 */
$fieldData = $results[0]; ?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-sm-4">
                    <span class="text-muted"><?= phrase('Language') ?></span>
                </div>
                <div class="col">
                    <span><?= $fieldData->language ?? null ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <span class="text-muted"><?= phrase('Alias') ?></span>
                </div>
                <div class="col">
                    <span><?= $fieldData->description ?? null ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <span class="text-muted"><?= phrase('Code') ?></span>
                </div>
                <div class="col">
                    <span><?= $fieldData->code ?? null ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <span class="text-muted"><?= phrase('Locale') ?></span>
                </div>
                <div class="col">
                    <span><?= $fieldData->locale ?? null ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <span class="text-muted"><?= phrase('Total Phrases') ?></span>
                </div>
                <div class="col">
                    <span><?= number_format($totalPhrases) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="<?= current_page(null, ['page' => null]) ?>" method="GET">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="<?= phrase('Search phrase') ?>" value="<?= service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null ?>" />
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="mx--3" />

    <?= pagination($pagination) ?>

    <hr class="mx--3" />

    <form action="<?= current_page() ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="row">
            <?php foreach ($phrases as $key => $val): ?>
                <?php $scope = $phraseScopes->$key ?? 'core'; ?>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <input type="hidden" name="phrase_scopes[<?= $key ?>]" value="<?= htmlspecialchars($scope) ?>" />
                            <input type="text" name="phrases[<?= $key ?>]" class="form-control" value="<?= $val ?>" placeholder="<?= $key ?>" data-bs-toggle="tooltip" title="<?= $key ?>" autocomplete="off" />
                            <a href="<?= current_page('delete_phrase', [
                              'phrase' => $key,
                              'scope' => $scope,
                            ]) ?>" class="btn border --open-delete-confirm" data-bs-toggle="tooltip" title="<?= phrase('Delete Phrase') ?>">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="opt-btn-overlap-fix"></div>
        <div class="row opt-btn">
            <div class="col-12 col-md-12 d-flex justify-content-between align-items-center gap-2">
                <a href="<?= current_page('../', [
                  'id' => null,
                  'code' => null,
                  'page' => null,
                  'q' => null,
                ]) ?>" class="btn btn-link --xhr">
                    <i class="mdi mdi-arrow-left"></i> <?= phrase('Back') ?>
                </a>
                <div class="d-flex justify-content-end align-items-center gap-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check"></i> <?= phrase('Translate') ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
