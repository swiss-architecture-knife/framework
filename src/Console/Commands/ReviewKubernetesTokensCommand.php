<?php
declare(strict_types=1);

namespace Swark\Console\Commands;


use Dreitier\Alm\Inspecting\Kubernetes\ClientContextFactory;
use Dreitier\Alm\Inspecting\Kubernetes\ClientContext;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReviewKubernetesTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'k8:token-review {--kube-config=} {--kube-user=} {--kube-cluster=} {--kube-context=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reviews Kubernetes secrets';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $factory = new ClientContextFactory();
        $context = $factory->createFromKubeConfig(
            kubeConfigPath: $this->option('kube-config'),
            user: $this->option('kube-user'),
            cluster: $this->option('kube-cluster'),
            context: $this->option('kube-context'),
            // enableDebug: true,
        );

        $tokens = $context->client->tokens()->find();
        $data = [];

        foreach ($tokens as $token) {
            $data[] = [$token->getKind(), $token->getName(), $token->getUserId(), $token->getDescription(), $token->isExpired() ? "true" : "false", $token->getExpiresAt(), Str::mask($token->getToken(), '*', 5)];
        }

        $this->table(['Kind', 'Name', 'User-ID', 'Description', 'Expired', 'Expiration at', 'Token'], $data);
    }
}
