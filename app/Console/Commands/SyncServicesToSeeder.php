<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servico;
use Illuminate\Support\Facades\File;

class SyncServicesToSeeder extends Command
{
    protected $signature = 'services:sync-seeder';
    protected $description = 'Sincroniza os serviços do banco de dados com o seeder';

    public function handle()
    {
        $this->info('🔄 Sincronizando serviços com o seeder...');

        // Buscar todos os serviços ativos
        $servicos = Servico::where('ativo', true)->get();

        if ($servicos->isEmpty()) {
            $this->warn('Nenhum serviço ativo encontrado.');
            return;
        }

        // Gerar conteúdo do seeder
        $seederContent = $this->generateSeederContent($servicos);

        // Salvar no arquivo do seeder
        $seederPath = database_path('seeders/ServicosSeeder.php');
        File::put($seederPath, $seederContent);

        $this->info("✅ Seeder atualizado com {$servicos->count()} serviços!");
        $this->line("📁 Arquivo: {$seederPath}");
    }

    private function generateSeederContent($servicos)
    {
        $content = "<?php\n\n";
        $content .= "namespace Database\Seeders;\n\n";
        $content .= "use Illuminate\Database\Seeder;\n";
        $content .= "use App\Models\Servico;\n\n";
        $content .= "class ServicosSeeder extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        // Serviços sincronizados automaticamente em " . now()->format('d/m/Y H:i:s') . "\n";
        $content .= "        // Total de serviços: {$servicos->count()}\n\n";

        foreach ($servicos as $servico) {
            $content .= "        Servico::updateOrCreate(\n";
            $content .= "            ['nome' => '" . addslashes($servico->nome) . "'],\n";
            $content .= "            [\n";
            $content .= "                'nome' => '" . addslashes($servico->nome) . "',\n";
            $content .= "                'descricao' => '" . addslashes($servico->descricao) . "',\n";
            $content .= "                'preco' => " . $servico->preco . ",\n";
            $content .= "                'duracao' => " . $servico->duracao . ",\n";
            $content .= "                'ativo' => true,\n";
            $content .= "            ]\n";
            $content .= "        );\n\n";
        }

        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }
}