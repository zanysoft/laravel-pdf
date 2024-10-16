<?php

namespace ZanySoft\LaravelPDF;

use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Mpdf\Utils\UtfString;
use View;

class PDF extends Mpdf
{

    protected $config = [];

    protected $filename = 'document.pdf';

    /**
     * @param $configs
     * @throws \Mpdf\MpdfException
     */
    public function __construct($configs = [])
    {

        $config = Config::get('pdf');

        if (!$config) {
            $config = include(__DIR__ . '/../config/pdf.php');
        }

        $this->config = array_merge($config, $configs);

        parent::__construct([
                'mode' => $this->getConfig('mode'), // mode - default ''
                'format' => $this->getConfig('format', 'A4'), // format - A4, for example, default ''
                'default_font_size' => $this->getConfig('default_font_size'),// font size - default 0
                'default_font' => $this->getConfig('default_font'), // default font family
                'margin_left' => $this->getConfig('margin_left', 10), // margin_left
                'margin_right' => $this->getConfig('margin_right', 10), // margin right
                'margin_top' => $this->getConfig('margin_top', 10), // margin top
                'margin_bottom' => $this->getConfig('margin_bottom', 10), // margin bottom
                'margin_header' => $this->getConfig('margin_header', 0), // margin header
                'margin_footer' => $this->getConfig('margin_footer', 0), // margin footer
                'orientation' => $this->getConfig('orientation', 'P'), // L - landscape, P - portrait
            ]
        );

        $this->SetTitle($this->getConfig('title'));
        $this->SetAuthor($this->getConfig('author'));

        $show_watermark = $this->getConfig('show_watermark');
        $watermark = $this->getConfig('watermark');

        if ($show_watermark && $watermark) {
            $this->showWatermarkText = $show_watermark;
            $this->SetWatermarkText($watermark);
        }

        if ($display_mode = $this->getConfig('display_mode')) {
            $this->SetDisplayMode($display_mode);
        }

        $direction = ($this->getConfig('dir') ?: $this->getConfig('direction')) == 'rtl' ? 'rtl' : 'ltr';

        $this->SetDirectionality($direction);

        if ($watermark_font = $this->getConfig('watermark_font')) {
            $this->watermark_font = $watermark_font;
        }

        $this->watermarkTextAlpha = $this->getConfig('watermark_text_alpha', 0.1);

        if ($custom_font_path = Config::get('pdf.custom_font_path')) {
            $custom_font_path = rtrim(str_replace('\\', '/', $custom_font_path) . '/') . '/';
            $this->AddFontDirectory($custom_font_path);
        }
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function Make(string $filename = ''): PDF
    {
        if ($filename) {
            $this->filename = $filename;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            return Config::get('pdf.' . $key, $default);
        }
    }

    /**
     * @param $dir
     * @return $this
     */
    public function SetDirection($dir)
    {
        $this->SetDirectionality($dir);

        return $this;
    }

    /**
     * @param string|Htmlable $html
     * @return void
     * @throws \Mpdf\MpdfException
     */
    public function loadHTML(string|Htmlable $html)
    {
        if ($html instanceof Htmlable) {
            $html = $html->toHtml();
        }

        $wm = UtfString::strcode2utf($html);

        $this->WriteHTML($wm);
    }

    /**
     * @param $file File path
     * @param $config
     * @return void
     * @throws \Mpdf\MpdfException
     */
    public function loadFile(string $file)
    {
        $this->WriteHTML(File::get($file));
    }

    /**
     * @param $view
     * @param $data
     * @param $mergeData
     * @return void
     * @throws \Mpdf\MpdfException
     */
    public function loadView($view, $data = [], $mergeData = [])
    {
        $this->WriteHTML(View::make($view, $data, $mergeData)->render());
    }

    /**
     * Add custom font to pdf
     *
     * $fontdata = [
     *       'sourcesanspro' => [
     *           'R' => 'SourceSansPro-Regular.ttf',
     *           'B' => 'SourceSansPro-Bold.ttf',
     *       ],
     *   ];
     *
     * @param array $fontdata
     * @param bool $is_unicode
     * @return void
     * @throws Exception
     */
    public function addCustomFont(array $fontdata, bool $is_unicode = false)
    {

        if (empty($fontdata) || !isset($fontdata)) {
            throw new Exception('Please add font data in EmbedFont() function.');
        }

        $custom_font_path = $this->getConfig('custom_font_path');
        if (!$custom_font_path) {
            throw new Exception('custom_font_path not set in "config/pdf.php" file.');
        } else {
            $custom_font_path = rtrim(str_replace('\\', '/', $custom_font_path), '/');
        }

        foreach ($fontdata as $f => $fs) {
            if (is_array($fs)) {
                foreach (['R', 'B', 'I', 'BI'] as $style) {
                    if (isset($fs[$style]) && $fs[$style]) {
                        $font = $fs[$style];
                        $font_file = $custom_font_path . '/' . $font;
                        if (!file_exists($font_file)) {
                            throw new Exception('Your font file "' . $font_file . '" not exist.');
                        }
                    }
                }
            }
        }

        $this->addFontData($fontdata, $is_unicode);
    }

    /**
     * @param $fonts
     * @param $unicode
     * @return void
     */
    protected function addFontData($fonts, $unicode = false)
    {
        foreach ($fonts as $key => $val) {
            $key = strtolower($key);
            if (is_array($val)) {

                foreach (['R', 'B', 'I', 'BI'] as $style) {
                    if (isset($val[$style]) && $val[$style]) {
                        $font = $val[$style];
                        $this->available_unifonts[] = $key . trim($style, 'R');
                    }
                }
                if ($unicode) {
                    $val['useKashida'] = 75;
                    $val['useOTL'] = 0xFF;
                }
                $this->fontdata[$key] = $val;
            }
        }

        $this->default_available_fonts = $this->available_unifonts;
    }

    /**
     * @param $arr
     * @return string
     */
    protected function array2str($arr)
    {
        $retStr = '';
        if (is_array($arr)) {
            $retStr .= "[ \r";
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $retStr .= "\t'" . $key . "' => " . $this->array2str($val) . ",\r";
                } else {
                    if (is_string($val)) {
                        $retStr .= "\t'" . $key . "' => '" . $val . "',\r";
                    } else {
                        $retStr .= "\t'" . $key . "' => " . ($key == 'useOTL' ? '0xFF' : $val) . ",\r";
                    }
                }
            }
            $retStr .= " ]";
        }

        return $retStr;
    }

    /**
     * @param string $name
     * @return string|null
     * @throws \Mpdf\MpdfException
     */
    public function embed(string $filename = '')
    {
        return $this->Output($filename ?: $this->filename, Destination::STRING_RETURN);
    }

    /**
     * @param string $filename
     * @return string|null
     * @throws \Mpdf\MpdfException
     */
    public function save(string $filename = '')
    {
        return $this->Output($filename ?: $this->filename, Destination::STRING_RETURN);
    }

    /**
     * @param string $filename
     * @return string|null
     * @throws \Mpdf\MpdfException
     */
    public function download(string $filename = '')
    {
        return $this->Output($filename ?: $this->filename, Destination::DOWNLOAD);
    }

    /**
     * @param string $filename
     * @return string|null
     * @throws \Mpdf\MpdfException
     */
    public function stream(string $filename = '')
    {
        return $this->Output($filename ?: $this->filename, Destination::INLINE);
    }
}
