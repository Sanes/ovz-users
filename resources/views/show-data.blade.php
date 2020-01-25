<div class="uk-grid-small uk-text-small" uk-grid>   
    <div class="uk-width-1-3@m">
      <div class="uk-box-shadow-large uk-padding-small uk-margin-medium-bottom">
        @if( $stat['disk']['usedPercent'] == null )
        <h4 class="uk-heading-divider">Нагрузка</h4>         
          <div class="uk-flex uk-flex-middle uk-flex-center uk-text-center uk-height-small">
            <div>
                <span uk-icon="warning"></span> <br>
                Статистика недоступна
                @if($data['State'] == 'running') <br><a href="#agent" uk-toggle>Установить агент</a>
                @endif
            </div>
          </div>
        @else
        <h4 class="uk-heading-divider">Нагрузка</h4>                
        <div class="uk-flex uk-flex-between uk-width-expand">
            <div>CPU</div>
            <div>{{ round($stat['cpu'], 0) }} %</div>
        </div>
        <progress class="uk-progress uk-margin-remove-top" value="{{ $stat['cpu'] }}" max="100" style="background: #e8e8e8;"></progress>
        <div class="uk-flex uk-flex-between uk-width-expand">
            <div>RAM</div>
            <div>{{ round($stat['memory']['usedPercent'], 0) }} %</div>
        </div>
        <progress class="uk-progress uk-margin-remove-top" value="{{ $stat['memory']['usedPercent'] }}" max="100" style="background: #e8e8e8;"></progress>
        <div class="uk-flex uk-flex-between uk-width-expand">
            <div>Диск</div>
            <div>{{ round($stat['disk']['usedPercent'], 0) }} %</div>
        </div>
        <progress class="uk-progress uk-margin-remove-top" value="{{ $stat['disk']['usedPercent'] }}" max="100" style="background: #e8e8e8;"></progress>
        <div class="uk-flex uk-flex-between uk-width-expand">
            <div>Uptime</div>
            <div>{{ gmdate("z\d H\h i\m", $data['Uptime']) }}</div>
        </div>
        @endif   
      </div> 
      <div class="">
        <a href="/ct/{{ $data['Name'] }}/edit" class="uk-icon-button uk-margin-small-right" uk-icon="settings" uk-tooltip="Настроить"></a>
        @if($data['State'] == 'running')
        <a href="#restart" class="uk-icon-button uk-margin-small-right" uk-icon="refresh" uk-tooltip="Перезапустить" uk-toggle></a>
        <a href="#stop" class="uk-icon-button uk-margin-small-right" uk-icon="close" uk-tooltip="Остановить" uk-toggle></a>
        @else
        <a href="/ct/{{ $data['Name'] }}/state/start" class="uk-icon-button uk-margin-small-right" uk-icon="play" uk-tooltip="Запустить"></a>      
        @endif
        <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="cog" uk-tooltip="Форматировать"></a>
        <a href="/ct" class="uk-icon-button uk-margin-small-right" uk-icon="grid" uk-tooltip="Контейнеры"></a>
      </div>     
    </div>
    <div class="uk-width-2-3@m uk-margin-small-bottom">
        <div class="uk-padding-small">            
            <h4 class="uk-heading-divider">Конфигурация</h4>  
            <div class="uk-grid-small"  uk-grid>
                <div class="uk-width-1-2@m">                            
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>Шаблон ОС</div>
                        <div><img src="/img/{{ $data['OS'] }}.png" alt=""></div>
                    </div>                                 
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>Статус</div>                         
                          @if($data['State'] == 'running')
                          <div class="uk-text-capitalize uk-text-success">Running</div> 
                          @else
                          <div class="uk-text-capitalize uk-text-muted">Stopped</div> 
                          @endif
                    </div> 
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>Имя</div>
                        <div>{{ $data['Name'] }}</div>
                    </div>                           
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>Hostname</div>
                        <div>{{ $data['Hostname'] }}</div>
                    </div>                             
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>DNS</div>
                        <div>{{ $data['DNS Servers'] }}</div>
                    </div>    
                    @if(isset($data['Description']))
                    <p>{{ $data['Description'] }}</p>
                    @endif
                </div>
                <div class="uk-width-1-2@m">                         
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>CPU</div>
                        <div>{{ $data['Hardware']['cpu']['cpus'] }} vCore</div>
                    </div>  
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>RAM</div>
                        <div>{{ str_replace('Mb', ' Mb', $data['Hardware']['memory']['size']) }}</div>
                    </div>                        
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>Диск</div>
                        <div>{{ round(str_replace('Mb', '', $data['Hardware']['hdd0']['size'])/1024, 2) }} Gb</div>
                    </div>                          
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand" uk-leader>IPs</div>
                        <div class="uk-text-right">
                          @if(isset($data['Hardware']['venet0']['ips'])){!! preg_replace('/\/\d+.\d+.\d+.\d+/', '<br>', $data['Hardware']['venet0']['ips']) !!}
                          @else
                          -
                          @endif                          
                        </div>
                    </div>                         
                </div>             
            </div>          
        </div>
    </div>
</div>



