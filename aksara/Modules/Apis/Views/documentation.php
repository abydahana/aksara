<?php
    $selected = service('request')->getGet('group');
    $group_collector = [];
    $access_token = false;
    $method = [];

    if ($permission->groups) {
        $groups = null;
        $privileges = [];

        foreach ($permission->groups as $key => $val)
        {
            $group_collector[] = $val->group_id;
            $actions = null;
            $extract_privileges = json_decode($val->group_privileges);

            if (isset($extract_privileges->$active)) {
                foreach ($extract_privileges->$active as $_key => $_val)
                {
                    $actions .= '<a href="#--method-' . $_val . '"><span class="badge bg-success"><i class="mdi mdi-link"></i> ' . phrase($_val) . '</span></a>&nbsp;';
                }
            }

            if ($val->group_id) {
                $access_token = true;
            }

            $groups .= '<option value="' . $val->group_id . '"' . ($val->group_id == $selected ? ' selected' : null) . '>' . $val->group_name . '</option>';

            $privileges[$selected] = $actions;
        }
    }
?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-3">
            <div class="sticky-top">
                <div class="pretty-scrollbar">
                    <a href="<?= base_url('apis/documentation'); ?>" class="<?= (! $active ? 'text-primary fw-bold' : null); ?> --xhr">
                        <?= phrase('Getting Started'); ?>
                    </a>
                    <br />
                    
                    <?php
                        if ($modules) {
                            foreach ($modules as $key => $val) {
                                echo '
                                    <a href="' . current_page(null, ['slug' => $val, 'group' => null]) . '" class="' . ($val == $active ? ' text-primary fw-bold' : null) . ' --xhr">
                                        ' . str_replace('/', ' &gt; ', $val) . '
                                    </a>
                                    <br />
                                ';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <?php if ($active): ?>
                <?php if ($permission->groups): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3 --title">
                                <?= $active; ?>
                            </h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>
                                        <?= phrase('Group Name'); ?>
                                    </th>
                                    <th>
                                        <?= phrase('Privileges'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="250">
                                        <form action="<?= current_page(); ?>" method="GET">
                                            <select name="group" class="form-control form-control-sm">
                                                <?= $groups; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?= (isset($privileges[$selected]) ? $privileges[$selected] : null); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h5 class="mt-3">
                        <?= phrase('Request Method'); ?>
                    </h5>
                <?php endif; ?>
                <?php if ($permission->privileges): ?>
                    <?php foreach ($permission->privileges as $key => $val): ?>
                        <?php $method[] = $val; ?>
                        <div class="mb-3" id="--method-<?= $val; ?>">
                            <h5 class="mb-1">
                                <span class="badge bg-primary bg-md">
                                    <?= (in_array($val, ['create', 'update']) ? 'POST' : (in_array($val, ['delete']) ? 'DELETE' : 'GET')); ?>
                                </span>
                            </h5>
                            <div class="rounded pt-2 pe-3 pb-2 ps-3 bg-dark">
                                <code class="text-light"><?= base_url(('index' !== $val ? $active . '/' . $val : $active)); ?></code>
                            </div>
                        </div>
                        <h5 class="mt-3">
                            <?= phrase('Header'); ?>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>
                                        <?= phrase('Field'); ?>
                                        </th>
                                        <th>
                                        <?= phrase('Type'); ?>
                                        </th>
                                        <th>
                                        <?= phrase('Description'); ?>
                                        </th>
                                        <th width="100" class="text-center">
                                        <?= phrase('Required'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <code>
                                                X-API-KEY
                                            </code>
                                        </td>
                                        <td>
                                            String
                                        </td>
                                        <td>
                                        <?= phrase('Valid API Key added in API Service'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                            <?= phrase('Required'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($access_token): ?>
                                    <tr>
                                        <td>
                                            <code>
                                                X-ACCESS-TOKEN
                                            </code>
                                        </td>
                                        <td>
                                            String
                                        </td>
                                        <td>
                                        <?= phrase('The given token from authentication response'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                            <?= phrase('Required'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center --spinner">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <div class="--query-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Query String'); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                            <?= phrase('Field'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Type'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Description'); ?>
                                            </th>
                                            <th width="100" class="text-center">
                                            <?= phrase('Required'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="--parameter-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Parameter'); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                            <?= phrase('Field'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Type'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Max Length'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Description'); ?>
                                            </th>
                                            <th width="100" class="text-center">
                                            <?= phrase('Required'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="--response-success-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Success Response'); ?>
                            </h5>
                            <pre class="language-javascript rounded mt-0"><code>{}</code></pre>
                        </div>
                        <div class="--response-error-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Error Response'); ?>
                            </h5>
                            <pre class="language-javascript rounded mt-0"><code>{}</code></pre>
                        </div>
                        <br />
                        <br />
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <h4 class="mb-3">
                    Introduction
                </h4>
                <p>
                    <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a> built with capability to deliver the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> output without building the another controller to produce the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> request and response. The concept and workflow of the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> implementation is just same as you accessing the application built with <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>, which is you are open now.
                </p>
                <p>
                    You will no longer need to think about complicated things that burden your work. All the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> request will be deliver through the authentication (handshake), permission checks, including the validation that you have defined for each existing or future modules.
                </p>
                <p>
                    Is it that easy? Yes, because this is <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>!
                </p>
                <hr />
                <h4 class="mb-3">
                    Getting Started
                </h4>
                <p>
                    To be able to use the API request feature, you need to first add an API key from the <a href="<?= go_to('../services'); ?>" class="text-primary"><b>API Services Management</b></a> menu. Specify the allowed request method, the allowed IP range and also the expiration date of the generated API key.
                </p>
                <p>
                    Use the generated API key to a specific client in the HEADER property when making a request.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Field'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                    <?= phrase('Description'); ?>
                                </th>
                                <th width="100" class="text-center">
                                    <?= phrase('Required'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        X-API-KEY
                                    </code>
                                </td>
                                <td>
                                    String
                                </td>
                                <td>
                                    <?= phrase('Valid API Key added in API Service'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                        <?= phrase('Required'); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>
                    For modules that require permission as specified for specific user groups, add the <code>X-ACCESS-TOKEN</code> parameter to the client HEADER and set the value with the token obtained from the authentication request.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Field'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                <?= phrase('Description'); ?>
                                </th>
                                <th width="100" class="text-center">
                                <?= phrase('Required'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        X-ACCESS-TOKEN
                                    </code>
                                </td>
                                <td>
                                    String
                                </td>
                                <td>
                                <?= phrase('The given token from authentication response'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                    <?= phrase('Required'); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>
                    The authentication process can be POST to <code><?= base_url('auth'); ?></code> by adding the <code>X-API-KEY</code> on the client HEADER, including the form data (form-data) to send the <code>username</code> and <code>password</code> of the user.
                </p>
                <hr />
                <h4>
                    Retrieving the Query Builder
                </h4>
                <p>
                    When you are requesting the data, there are query string helper (which mentioned under the "<code>query_string</code>") that will help you to retrieving the data to be matched with the query string keywords. Besides that, this also available query string helper to retrieving the specified results.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Key'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                    <?= phrase('Description'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        limit
                                    </code>
                                </td>
                                <td>
                                    int
                                </td>
                                <td>
                                    Applying the result limit
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        offset
                                    </code>
                                </td>
                                <td>
                                    int
                                </td>
                                <td>
                                    Applying the result offset
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        order
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                    Field name to be ordered
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        sort
                                    </code>
                                </td>
                                <td>
                                    string <code>ASC|DESC</code>
                                </td>
                                <td>
                                    Sort order
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        q
                                    </code>
                                </td>
                                <td>
                                    string | number | int
                                </td>
                                <td>
                                    The keyword to applying search
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        column
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                    Specified field to apply the specific search
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if (UA !== 'mobile' && typeof mCustomScrollbar === 'function') {
            $('.pretty-scrollbar').mCustomScrollbar({
                autoHideScrollbar: true,
                axis: 'y',
                scrollInertia: 170,
                mouseWheelPixels: 170,
                setHeight: $(window).outerHeight(true) - (($('[role=header]').outerHeight(true) ?? 0) + ($('[role=breadcrumb]').outerHeight(true) ?? 0) + ($('[role=meta]').outerHeight(true) ?? 0)),
                advanced: {
                    updateOnContentResize: true
                },
                autoHideScrollbar: false
            })
        }
        
        $.ajax({
            url: '<?= current_page(); ?>',
            context: this,
            method: 'POST',
            data: {
                mode: 'fetch',
                group: '<?= ($selected ? $selected : (isset($group_collector[0]) ? $group_collector[0] : 0)); ?>',
                method: JSON.parse('<?= json_encode($method); ?>')
            },
            beforeSend: function() {
            }
        })
        .done(function(response, status, error) {
            $('.--spinner').remove();
            
            if (response.results) {
                $.each(response.results, function(key, val) {
                    if (typeof val.query_string !== 'undefined') {
                        $.each(val.query_string, function(_key, _val) {
                            if ($('.--query-' + key).hasClass('d-none')) {
                                $('.--query-' + key).removeClass('d-none')
                            }
                            
                            $('<tr><td><code>' + _key + '</code></td><td>mixed</td><td>-</td><td class="text-center"><span class="badge bg-danger"><?= phrase('Required'); ?></span></td></tr>').appendTo('.--query-' + key + ' tbody')
                        })
                    }
                    
                    if (typeof val.parameter !== 'undefined') {
                        $.each(val.parameter, function(_key, _val) {
                            if ($('.--parameter-' + key).hasClass('d-none')) {
                                $('.--parameter-' + key).removeClass('d-none')
                            }
                            
                            $('<tr><td><code>' + _key + '</code></td><td>' + _val.type + '</td><td>' + _val.maxlength + '</td><td>' + _val.label + '</td><td class="text-center">' + (_val.required ? '<span class="badge bg-danger"><?= phrase('Required'); ?></span>' : '') + '</td></tr>').appendTo('.--parameter-' + key + ' tbody')
                        })
                    }
                    
                    if (typeof val.response.success !== 'undefined') {
                        if ($('.--response-success-' + key).hasClass('d-none')) {
                            $('.--response-success-' + key).removeClass('d-none')
                        }
                        
                        $('.--response-success-' + key + ' pre code').text(JSON.stringify(val.response.success, null, 4))
                    }
                    
                    if (typeof val.response.error !== 'undefined') {
                        if ($('.--response-error-' + key).hasClass('d-none')) {
                            $('.--response-error-' + key).removeClass('d-none')
                        }
                        
                        $('.--response-error-' + key + ' pre code').text(JSON.stringify(val.response.error, null, 4))
                    }
                })
            }
        })
    })
</script>
