<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ContainerUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:update {--id=}{--cpus=}{--memsize=}{--size=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update config';

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
        $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));

        $stream = ssh2_exec($connection, 
            'prlctl set '.$this->option('id').' --cpus '.$this->option('cpus').' --memsize '.$this->option('memsize').'G --size='.$this->option('size').'G'
            );

        stream_set_blocking($stream, true);
        $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $result_err = stream_get_contents($stream_err);
        $this->line('OK');
    }
}
