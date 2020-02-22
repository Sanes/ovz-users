<?php

namespace App\Console\Commands;
use App\Container; 
use Illuminate\Console\Command;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;


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
        $key = new RSA();
        $key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
        $ssh = new SSH2(config('ovz.ssh_ip'));
        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }
        $result = $ssh->exec('prlctl delete '.$this->option('id').' --force');
        Container::where('name', $this->option('id'))->delete();
        $this->line('OK');
    }
}
