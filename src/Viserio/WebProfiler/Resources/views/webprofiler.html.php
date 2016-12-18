<?php
use Viserio\WebProfiler\Util\TemplateHelper;

if (! isset($token, $menus, $icons, $panels)) {
    return;
}
?>
<div id="webprofiler" data-token="webprofiler-<?php echo TemplateHelper::escape($token); ?>" class="webprofiler">
    <a class="webprofiler-show-button" title="Show WebProfiler" tabindex="-1" accesskey="D">
        <?php echo file_get_contents($icons['ic_narrowspark_white_24px.svg']); ?>
    </a>
    <div class="webprofiler-header">
        <?php if (count($menus) !== 0): ?>
        <div class="webprofiler-menus">
            <a class="webprofiler-hide-button" title="Close WebProfiler" tabindex="-1" accesskey="D">
                <?php echo file_get_contents($icons['ic_clear_white_24px.svg']); ?>
            </a>
            <?php foreach ($menus as $name => $menu):
                $tooltip = false;

                if (isset($menu['tooltip'])) {
                    $tooltip = true;
                }

                $data = isset($panels[$name]) ? 'data-panel-target-id="webprofiler-panel-' . TemplateHelper::escape($name) . '"' : '';
                $hasPanels = isset($panels[$name]) ? ' webprofiler-menu-has-panel' : '';
                $hasTooltip = $tooltip ? ' webprofiler-menu-has-tooltip' : '';
                $cssClasses = isset($menu['menu']['class']) ? ' ' . $menu['menu']['class'] : '';
            ?>
            <a <?php echo $data ?> class="webprofiler-menu webprofiler-menu-<?php echo TemplateHelper::escape($name) ?> webprofiler-menu-position-<?php echo $menu['position'] . $hasPanels . $hasTooltip . $cssClasses; ?>">
                <div class="webprofiler-menu-content">
                    <?php if (isset($menu['menu']['icon'])): ?>
                    <span class="webprofiler-menu-icon">
                        <?php echo isset($icons[$menu['menu']['icon']]) ? file_get_contents($icons[$menu['menu']['icon']]) : $menu['menu']['icon'] ?>
                    </span>
                    <?php endif; ?>
                    <?php if (isset($menu['menu']['status'])): ?>
                    <span class="webprofiler-menu-status">
                        <?php echo TemplateHelper::escape($menu['menu']['status']) ?>
                    </span>
                    <?php endif; ?>
                    <span class="webprofiler-menu-label">
                        <?php echo TemplateHelper::escape($menu['menu']['label']) ?>
                    </span>
                    <span class="webprofiler-menu-value">
                        <?php echo TemplateHelper::escape($menu['menu']['value']) ?>
                    </span>
                </div>
                <?php if ($tooltip): ?>
                    <div class="webprofiler-menu-tooltip">
                        <?php echo $menu['tooltip'] ?>
                    </div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="webprofiler-body">
        <div id="webprofiler-body-dragbar"></div>
        <?php foreach ($panels as $name => $panel): ?>
        <div class="webprofiler-panel webprofiler-panel-<?php echo TemplateHelper::escape($name) . TemplateHelper::escape($panel['class']); ?>">
            <?php echo $panel['content'] ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
