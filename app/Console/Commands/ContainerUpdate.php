<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;


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

        $sizeByte = ($this->option('size')-1)*1024*1024*1024;
        $key = new RSA();
        $key->loadKey(file_get_contents(config('ovz.ssh_rsa')));
        $ssh = new SSH2(config('ovz.ssh_ip'));
        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }        

        $resultStat = $ssh->exec('prlctl exec '.$this->option('id').' /usr/local/bin/monit'); 
        $responseStat = json_decode($resultStat, true);

        if (!isset($responseStat['diskUsed'])) {
            $this->line('Fail monit');
        }
        elseif ($sizeByte < $responseStat['diskUsed']) {
            $this->line('Fail size');
        }
        else {

        $result = $ssh->exec('prlctl set '.$this->option('id').' --cpus '.$this->option('cpus').' --memsize '.$this->option('memsize').'G --size='.$this->option('size').'G');
        $this->line('OK');
        }
    }
}
