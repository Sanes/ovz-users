<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Container; 
use App\User; 
use App\Ip4address; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class ContainerCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:create {--email=}{--ostemplate=}{--cpus=}{--memsize=}{--ipadd=}{--size=}{--hostname=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create container';

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


        $getAddress = Ip4address::where('container_id', null);

        if ($getAddress->count()) {
            $getAddress = Ip4address::where('container_id', null)->first();
            $getUser = User::where('email', $this->option('email'));

            if ($getUser->count()) {
                $id = Container::create()->id;
                $userInfo = User::where('email', $this->option('email'))->first();
                Container::where('id', $id)->update(['name' => 'ct'.$id, 'user_id' => $userInfo->id, 'locked' => false]);
                Ip4address::where('id', $getAddress->id)->update(['container_id' => $id]);

                $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
                ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));

                $stream = ssh2_exec($connection, 
                    'prlctl create ct'.$id.' --vmtype ct --ostemplate '.$this->option('ostemplate').';sleep 2;prlctl set ct'.$id.' --hostname '.$this->option('hostname').' --cpus '.$this->option('cpus').' --memsize '.$this->option('memsize').'G --ipadd '.$getAddress->address.';prlctl set ct'.$id.' --size='.$this->option('size').'G; prlctl start ct'.$id
                    );

                stream_set_blocking($stream, true);
                $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                // $result_err = stream_get_contents($stream_err);
                $this->line('OK --id=ct'.$id.' --ctname=ct'.$id.' --memsize='.$this->option('memsize').' --size='.$this->option('size').' --cpus='.$this->option('cpus').' --ipadd='.$getAddress->address).' --hostname='.$this->option('hostname');

            } 

                else {
                $user = new User();
                $passwduser = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 12);
                $user->password = Hash::make($passwduser);
                $user->email = $this->option('email');
                $user->name = $this->option('email');
                $user->save();

                $userInfo = User::where('email', $this->option('email'))->first();
                $id = Container::create()->id;
                Container::where('id', $id)->update(['name' => 'ct'.$id, 'user_id' => $userInfo->id, 'locked' => false]);
                Ip4address::where('id', $getAddress->id)->update(['container_id' => $id]);


                $connection = ssh2_connect(config('ovz.ssh_ip'), config('ovz.ssh_port'), array('hostkey'=>'ssh-rsa'));
                ssh2_auth_pubkey_file($connection, config('ovz.ssh_user'), config('ovz.ssh_rsa_pub'), config('ovz.ssh_rsa'));
                $stream = ssh2_exec($connection, 
                    
                    'prlctl create ct'.$id.' --vmtype ct --ostemplate '.$this->option('ostemplate').';sleep 2;prlctl set ct'.$id.' --hostname '.$this->option('hostname').' --cpus '.$this->option('cpus').' --memsize '.$this->option('memsize').'G --ipadd '.$getAddress->address.';prlctl set ct'.$id.' --size='.$this->option('size').'G; prlctl start ct'.$id
                    );

                stream_set_blocking($stream, true);
                $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                $result_err = stream_get_contents($stream_err);
                $this->line('OK --id=ct'.$id.' --ctname=ct'.$id.' --memsize='.$this->option('memsize').' --size='.$this->option('size').' --cpus='.$this->option('cpus').' --ipadd='.$getAddress->address).' --hostname='.$this->option('hostname');

                }
            } 

            else {
                $this->line('False');
            }
    }
}
