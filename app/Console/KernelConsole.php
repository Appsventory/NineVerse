<?php

namespace App\Console;

class KernelConsole
{
    public function run(array $argv)
    {
        $command = $argv[1] ?? null;

        if (!$command || $command === 'help') {
            $this->showHelp();
            return;
        }

        switch ($command) {
            case 'make:controller':
                $name = $argv[2] ?? null;
                $makeModel = in_array('--model', $argv);
                $this->makeController($name, $makeModel);
                break;

            case 'make:model':
                $name = $argv[2] ?? null;
                $this->makeModel($name);
                break;

            case 'make:middleware':
                $name = $argv[2] ?? null;
                $this->makeMiddleware($name);
                break;

            case 'make:view':
                $name = $argv[2] ?? null;
                $this->makeView($name);
                break;

            case 'make:route':
                $name = $argv[2] ?? null;
                $options = array_slice($argv, 3);
                $this->makeRoute($name, $options);
                break;

            case 'server':
            case 'serve':
                $this->startServer($argv);
                break;



            default:
                echo "\e[31mUnknown command:\e[0m $command\n";
                $this->showHelp();
        }
    }

    protected function startServer(array $argv)
    {
        $defaultPort = 99;
        $port = (string) $defaultPort;

        foreach ($argv as $i => $arg) {
            if ($arg === '--p' && isset($argv[$i + 1])) {
                $port = $argv[$i + 1];
            }
        }

        if (!$this->isPortAvailable((int)$port)) {
            echo "\e[31m❌ Port $port is already in use. Please choose another port.\e[0m\n";
            exit(1);
        }

        $host = "localhost:$port";
        echo "\e[32m📡 Starting PHP development server at:\e[0m http://$host\n";
        echo "🔁 Press Ctrl+C to stop\n";
        passthru("php -S $host -t public");
    }

    protected function isPortAvailable(int $port): bool
    {
        $sock = @fsockopen('127.0.0.1', $port);
        if ($sock) {
            fclose($sock);
            return false;
        }
        return true;
    }

    public function showHelp()
    {
        echo "\n\e[1;33mFANY CLI Tool\e[0m\n";
        echo "Usage: \e[36mphp fany <command> [options]\e[0m\n";

        echo "\n\e[1;32mAvailable commands:\e[0m\n";
        echo "  \e[36mserver [--p <port>]\e[0m                  Start PHP dev server (default: 8000)\n";
        echo "  \e[36mmake:controller <Name> [--model]\e[0m     Create a new controller (optionally with model)\n";
        echo "  \e[36mmake:model <Name>\e[0m                    Create a new model\n";
        echo "  \e[36mmake:view <Name>\e[0m                     Create a new view (.nixs.php)\n";
        echo "  \e[36mmake:middleware <Name>\e[0m               Create a new middleware class\n";
        echo "  \e[36mmake:route <Name> [flags]\e[0m            Append route(s) to routes/web.php\n";
        echo "       \e[2m--G     Add GET     route → index()\e[0m\n";
        echo "       \e[2m--P     Add POST    route → create()\e[0m\n";
        echo "       \e[2m--U     Add PUT     route → update()\e[0m\n";
        echo "       \e[2m--D     Add DELETE  route → destroy()\e[0m\n";
        echo "       \e[2m--M=AuthMiddleware  Attach middleware\e[0m\n";
        echo "  \e[36mhelp\e[0m                              Show this help menu\n\n";
    }


    public function makeController($name, $withModel = false)
    {
        if (!$name) {
            echo "❌ Controller name is required.\n";
            return;
        }

        $className = ucfirst($name) . 'Controller';
        $modelName = ucfirst($name);
        $path = "app/Controllers/{$className}.php";

        if (file_exists($path)) {
            echo "⚠️  Controller $className already exists.\n";
            return;
        }

        $useModel = $withModel ? "use App\\Models\\{$modelName};\n" : '';

        $code = <<<EOT
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CsrfToken;
{$useModel}
class {$className} extends Controller
{
    public function index()
    {
        // Tampilkan semua data
    }

    public function create()
    {
        // Tampilkan form tambah
    }

    public function store()
    {
        // Simpan data baru
        CsrfToken::validate();
    }

    public function edit(\$id)
    {
        // Tampilkan form edit
    }

    public function update(\$id)
    {
        // Proses update data
        CsrfToken::validate();
    }

    public function destroy(\$id)
    {
        // Hapus data
        CsrfToken::validate();
    }
}
EOT;

        file_put_contents($path, $code);
        echo "✅ Controller created: $path\n";

        if ($withModel) $this->makeModel($name);
    }

    public function makeModel($name)
    {
        if (!$name) {
            echo "❌ Model name is required.\n";
            return;
        }

        $className = ucfirst($name);
        $path = "app/Models/{$className}.php";

        if (file_exists($path)) {
            echo "⚠️  Model $className already exists.\n";
            return;
        }

        $code = <<<EOT
<?php

namespace App\Models;

use App\Core\Model;

class {$className} extends Model
{
    //
}
EOT;

        file_put_contents($path, $code);
        echo "✅ Model created: $path\n";
    }

    public function makeMiddleware($name)
    {
        if (!$name) {
            echo "❌ Middleware name is required.\n";
            return;
        }

        $className = ucfirst($name) . 'Middleware';
        $path = "app/Middleware/{$className}.php";

        if (file_exists($path)) {
            echo "⚠️  Middleware $className already exists.\n";
            return;
        }

        $code = <<<EOT
<?php

namespace App\Middleware;

class {$className}
{
    public function handle()
    {
        // middleware logic
    }
}
EOT;

        file_put_contents($path, $code);
        echo "✅ Middleware created: $path\n";
    }

    public function makeView($name)
    {
        if (!$name) {
            echo "❌ View name is required.\n";
            return;
        }

        $path = "app/Views/" . str_replace('.', '/', $name) . ".nixs.php";

        if (file_exists($path)) {
            echo "⚠️  View $name already exists.\n";
            return;
        }

        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        file_put_contents($path, "<!-- View: $name -->\n");
        echo "✅ View created: $path\n";
    }

    public function makeRoute($name, $options = [])
    {
        if (!$name) {
            echo "❌ Route name is required.\n";
            return;
        }

        $route = strtolower($name);
        $controller = ucfirst($name) . 'Controller';
        $middleware = '';

        // Tangani middleware
        foreach ($options as $opt) {
            if (str_starts_with($opt, '--M=')) {
                $middlewareName = trim(substr($opt, 4));
                $middleware = "->middleware('$middlewareName')";
            }
        }

        $lines = [];

        foreach ($options as $opt) {
            switch ($opt) {
                case '--G':
                    $lines[] = "Router::get('$route', '$controller@index')$middleware;";
                    break;
                case '--P':
                    $lines[] = "Router::post('$route', '$controller@create')$middleware;";
                    break;
                case '--U':
                    $lines[] = "Router::put('$route', '$controller@update')$middleware;";
                    break;
                case '--D':
                    $lines[] = "Router::get('$route/{delete}', '$controller@destroy')$middleware;";
                    break;
            }
        }

        if (empty($lines)) {
            echo "⚠️ No method specified (--G, --P, --U, --D). Nothing was added.\n";
            return;
        }

        $output = "\n" . implode("\n", $lines) . "\n";
        file_put_contents('app/routes/web.php', $output, FILE_APPEND);
        echo "✅ Route(s) added to routes/web.php:\n" . $output;
    }
}
