<?php

namespace App\Console;

class Nixs
{
    protected static array $sections = [];
    protected static string $layout = '';
    protected static string $currentSection = '';

    public static function render($template, $data = [])
    {
        $path = "../app/Views/" . str_replace('.', '/', $template) . ".nixs.php";

        if (!file_exists($path)) {
            throw new \Exception("View {$template} not found.");
        }

        extract($data);

        ob_start();
        include self::compileToTemp($path);
        $content = ob_get_clean();

        // Jika menggunakan layout
        if (self::$layout) {
            $layoutPath = "../app/Views/" . str_replace('.', '/', self::$layout) . ".nixs.php";


            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout " . self::$layout . " not found.");
            }

            ob_start();
            include self::compileToTemp($layoutPath);
            echo ob_get_clean();
        } else {
            echo $content;
        }
    }

    protected static function compileToTemp($path): string
    {
        $raw = file_get_contents($path);
        $compiled = self::compile($raw);

        $tempPath = sys_get_temp_dir() . '/nixs_' . md5($path . microtime()) . '.php';
        file_put_contents($tempPath, $compiled);
        return $tempPath;
    }

    protected static function compile(string $content): string
    {
        // Kompilasi variabel
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>', $content);

        // Kompilasi direktif kontrol
        $directives = [
            '/@if\s*\((.*?)\)/'      => '<?php if ($1): ?>',
            '/@elseif\s*\((.*?)\)/'  => '<?php elseif ($1): ?>',
            '/@else/'                => '<?php else: ?>',
            '/@endif/'               => '<?php endif; ?>',
            '/@foreach\s*\((.*?)\)/' => '<?php foreach ($1): ?>',
            '/@endforeach/'          => '<?php endforeach; ?>',
            '/@for\s*\((.*?)\)/'     => '<?php for ($1): ?>',
            '/@endfor/'              => '<?php endfor; ?>',
            '/@while\s*\((.*?)\)/'   => '<?php while ($1): ?>',
            '/@endwhile/'            => '<?php endwhile; ?>',
            '/@php(.*?)@endphp/s'    => '<?php $1 ?>',
        ];

        foreach ($directives as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        // @extends('layouts.master')
        $content = preg_replace_callback('/@extends\(\'(.*?)\'\)/', function ($matches) {
            self::$layout = $matches[1];
            return '';
        }, $content);

        // @section('name') ... @endsection
        $content = preg_replace_callback('/@section\(\'(.*?)\'\)(.*?)@endsection/s', function ($matches) {
            $name = $matches[1];
            $code = $matches[2];
            self::$sections[$name] = $code;
            return '';
        }, $content);

        // @yield('name')
        $content = preg_replace_callback('/@yield\(\'(.*?)\'\)/', function ($matches) {
            $name = $matches[1];
            return self::$sections[$name] ?? '';
        }, $content);

        // @include('partial.name')
        $content = preg_replace_callback('/@include\(\'(.*?)\'\)/', function ($matches) {
            $includePath = "../app/Views/" . str_replace('.', '/', $matches[1]) . ".nixs.php";
            return file_exists($includePath) ? file_get_contents($includePath) : '';
        }, $content);

        // Terakhir, parse form method custom seperti PUT, DELETE, PATCH
        $content = self::parseCustomFormMethods($content);

        return $content;
    }

    protected static function parseCustomFormMethods(string $content): string
    {
        return preg_replace_callback(
            '/<form([^>]*?)method=["\'](PUT|DELETE|PATCH)["\'](.*?)>/i',
            function ($matches) {
                $attributes = $matches[1] . $matches[3];
                $method = strtoupper($matches[2]);
                $formTag = "<form{$attributes} method=\"POST\">";
                $hiddenInput = "\n    <input type=\"hidden\" name=\"_method\" value=\"{$method}\">";
                return $formTag . $hiddenInput;
            },
            $content
        );
    }
}
