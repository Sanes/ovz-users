<?php

namespace App\Console\Commands;
use App\Container; 
use Illuminate\Console\Command;

class ContainerClose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:close {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete container';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        // ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));

        $result = shell_exec('ssh -i '.config('ovz.ssh_rsa').' root@'.config('ovz.ssh_ip').' prlctl delete '.$this->option('id').' --force');

        // stream_set_blocking($stream, true);
        // $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        // $result_err = stream_get_contents($stream_err);
        Container::where('name', $this->option('id'))->delete();
        $this->line('OK');
    }
}
