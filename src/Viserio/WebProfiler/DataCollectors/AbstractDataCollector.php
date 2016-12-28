<?php
declare(strict_types=1);
namespace Viserio\WebProfiler\DataCollectors;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\ClassStub;
use Symfony\Component\VarDumper\Caster\LinkStub;
use Symfony\Component\VarDumper\Caster\StubCaster;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Viserio\Contracts\WebProfiler\DataCollector as DataCollectorContract;
use Viserio\Support\Debug\HtmlDumper;
use Viserio\Support\Str;
use Viserio\WebProfiler\Util\HtmlDumperOutput;

abstract class AbstractDataCollector implements DataCollectorContract
{
    /**
     * Array of all collected datas.
     *
     * @var array
     */
    protected $data;

    /**
     * Configured VarCloner instance.
     *
     * @var \Symfony\Component\VarDumper\Cloner\ClonerInterface
     */
    private static $cloner;

    /**
     * HtmlDumper instance.
     *
     * @var \Viserio\Support\Debug\HtmlDumper
     */
    private static $htmlDumper;

    /**
     * HtmlDumperOutput instance.
     *
     * @var \Viserio\WebProfiler\Util\HtmlDumperOutput
     */
    private static $htmlDumperOutput;

    /**
     * The cache of stubs.
     *
     * @var array
     */
    private static $stubsCache = [];

    /**
     * {@inheritdoc}
     */
    public function getMenuPosition(): string
    {
        return 'left';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        $namespace = mb_substr(get_called_class(), 0, mb_strrpos(get_called_class(), '\\'));

        return Str::snake(str_replace($namespace . '\\', '', get_class($this)), '-');
    }

    /**
     * Get all collected data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Add measurement to float time.
     *
     * @param float $seconds
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    protected function formatDuration(float $seconds): string
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } elseif ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }

        return round($seconds, 2) . 's';
    }

    /**
     * Convert a number string to bytes.
     *
     * @param string
     * @param string $memoryLimit
     *
     * @return int
     *
     * @codeCoverageIgnore
     */
    protected function convertToBytes(string $memoryLimit): int
    {
        if ($memoryLimit === '-1') {
            return -1;
        }

        $memoryLimit = mb_strtolower($memoryLimit);
        $max         = mb_strtolower(ltrim($memoryLimit, '+'));

        if (mb_strpos($max, '0x') === 0) {
            $max = intval($max, 16);
        } elseif (mb_strpos($max, '0') === 0) {
            $max = intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (mb_substr($memoryLimit, -1)) {
            case 't':
                $max *= 1024;
                break;
            case 'g':
                $max *= 1024;
                break;
            case 'm':
                $max *= 1024;
                break;
            case 'k':
                $max *= 1024;
                break;
        }

        return $max;
    }

    /**
     * Creates a tooltip group from array.
     *
     * array[]
     *     ['key']       string          Defines the name of <b> html element.
     *     ['value']     array|string    Defines the content to be shown in the <span> html element.
     *         ['class'] string          Adds a class to the <span> html element.
     *         ['value'] string          Adds the content.
     *
     * @param array $data (See above)
     *
     * @return string
     */
    protected function createTooltipGroup(array $data): string
    {
        $tooltip = '<div class="webprofiler-menu-tooltip-group">';

        foreach ($data as $strong => $infos) {
            $tooltip .= '<div class="webprofiler-menu-tooltip-group-piece">';

            if (is_array($infos)) {
                $tooltip .= '<b>' . $strong . '</b>';

                foreach ($infos as $info) {
                    $class = isset($info['class']) ? ' class="' . $info['class'] . '"' : '';

                    $tooltip .= '<span' . $class . '>' . $info['value'] . '</span>';
                }
            } elseif (is_int($strong) && is_string($infos)) {
                $tooltip .= $infos;
            } else {
                $tooltip .= '<b>' . $strong . '</b><span>' . $infos . '</span>';
            }

            $tooltip .= '</div>';
        }

        $tooltip .= '</div>';

        return $tooltip;
    }

    /**
     * Creates a tab slider.
     *
     * array[]         array     Defines witch tabs a shown.
     *     ['name']    string    Name of the tab
     *     ['content'] string    Tab content.
     *
     * @param array $data (See above)
     *
     * @return string
     */
    protected function createTabs(array $data): string
    {
        $grid = '';

        if (($counted = count($data)) < 12) {
            $grid = ' col span_' . floor(12 / $counted);
        }

        $html = '<div class="webprofiler-tabs' . ($grid !== '' ? ' row' : '') . '">';

        foreach ($data as $key => $value) {
            $id = uniqid($key . '-');

            $html .= '<div class="webprofiler-tabs-tab' . $grid . '">';
            $html .= '<input type="radio" name="tabgroup" id="tab-' . $id . '">';
            $html .= '<label for="tab-' . $id . '">' . $value['name'] . '</label>';
            $html .= '<div class="webprofiler-tabs-tab-content">';
            $html .= $value['content'];
            $html .= '</div></div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Creates a table.
     *
     * @param array       $data
     * @param string|null $name
     * @param array       $headers
     */
    protected function createTable(array $data, ?string $name = null, array $headers = ['Key', 'Value'])
    {
        $html = $name !== null ? '<h3>' . $name . '</h3>' : '';

        if (count($data) !== 0) {
            $html .= '<table class="row"><thead><tr>';

            foreach ($headers as $header) {
                $html .= '<th scope="col" class="' . $header . '">' . $header . '</th>';
            }

            $html .= '</tr></thead><tbody>';

            foreach ($data as $key => $values) {
                if (is_string($key)) {
                    $html .= '<tr>';
                    $html .= '<th>' . $key . '</th>';
                    $html .= '<td>' . $this->cloneVar($values) . '</td>';
                    $html .= '</tr>';
                } else {
                    $html .= '<tr>';

                    if (is_array($values)) {
                        foreach ($values as $key => $value) {
                            $html .= '<td>' . $this->cloneVar($value) . '</td>';
                        }
                    } else {
                        $html .= '<td>' . $this->cloneVar($values) . '</td>';
                    }

                    $html .= '</tr>';
                }
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<div class="empty">Empty</div>';
        }

        return $html;
    }

    /**
     * Create a dropdown menu content.
     *
     * @param array $data
     *
     * @return string
     */
    protected function createDropdownMenuContent(array $data)
    {
        $selects  = $content  = [];
        $selected = false;

        foreach ($data as $key => $value) {
            $id = 'content-' . $key . '-' . uniqid('');

            $selects[$key] = '<option value="' . $id . '"' . ($selected === false ? $selected = 'selected' : '') . '>' . $key . '</option>';
            $content[$key] = '<div id="' . $id . '" class="selected-content">' . $value . '</div>';
        }

        $html = '<select class="content-selector" name="' . $this->getName() . '">';

        foreach ($selects as $key => $value) {
            $html .= $value;
        }

        $html .= '</select>';

        foreach ($content as $key => $value) {
            $html .= $value;
        }

        return $html;
    }

    /**
     * Converts the variable into a serializable Data instance.
     *
     * @param mixed $var
     *
     * @return null|string
     */
    protected function cloneVar($var): ?string
    {
        $cloneVar = self::getCloner()->cloneVar($this->decorateVar($var), Caster::EXCLUDE_VERBOSE);
        $dumper   = self::getDumper();

        $dumper->dump(
            $cloneVar,
            self::$htmlDumperOutput
        );

        $output = self::$htmlDumperOutput->getOutput();
        self::$htmlDumperOutput->flush();

        return $output;
    }

    /**
     * Get the cloner used for dumping variables.
     *
     * @return \Symfony\Component\VarDumper\Cloner\AbstractCloner
     */
    private static function getCloner()
    {
        if (! self::$cloner) {
            self::$cloner = new VarCloner();
            self::$cloner->setMaxItems(250);
            self::$cloner->addCasters([
                Stub::class => function (Stub $v, array $a, Stub $s, $isNested) {
                    return $isNested ? $a : StubCaster::castStub($v, $a, $s, true);
                },
            ]);
        }

        return self::$cloner;
    }

    /**
     * Get a HtmlDumper instance.
     *
     * @return \Viserio\Support\Debug\HtmlDumper
     */
    private static function getDumper(): HtmlDumper
    {
        if (self::$htmlDumper === null) {
            self::$htmlDumperOutput = new HtmlDumperOutput();

            // re-use the same var-dumper instance, so it won't re-render the global styles/scripts on each dump.
            self::$htmlDumper = new HtmlDumper(self::$htmlDumperOutput);
        }

        return self::$htmlDumper;
    }

    /**
     * @param mixed $var
     *
     * @return mixed
     */
    private function decorateVar($var)
    {
        if (is_array($var)) {
            if (isset($var[0], $var[1]) && is_callable($var)) {
                return ClassStub::wrapCallable($var);
            }

            foreach ($var as $k => $v) {
                if ($v !== $d = $this->decorateVar($v)) {
                    $var[$k] = $d;
                }
            }

            return $var;
        }

        if (is_string($var)) {
            if (isset(self::$stubsCache[$var])) {
                return self::$stubsCache[$var];
            }

            if (mb_strpos($var, '\\') !== false) {
                $c = ($i = mb_strpos($var, '::') !== false) ? mb_substr($var, 0, $i) : $var;

                if (class_exists($c, false) || interface_exists($c, false) || trait_exists($c, false)) {
                    return self::$stubsCache[$var] = new ClassStub($var);
                }
            }

            if (mb_strpos($var, DIRECTORY_SEPARATOR) !== false &&
                mb_strpos($var, '://') !== false &&
                mb_strpos($var, "\0") && @is_file($var) === false
            ) {
                return self::$stubsCache[$var] = new LinkStub($var);
            }
        }

        return $var;
    }
}
