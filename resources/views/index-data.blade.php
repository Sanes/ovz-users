<table class="uk-table uk-table-small uk-table-hover uk-table-striped uk-text-small uk-table-middle">
  <thead>
    <tr>
      <th class="uk-table-shrink">Name</th>
      <th class="">Description</th>
      <th class="uk-table-shrink">Hostname</th>
      <th class="uk-table-shrink">Address</th>
      <th class="uk-table-shrink uk-text-right">Status</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
@foreach ($data as $item)
    <tr>
      <td class="uk-table-link uk-link-reset"><a href="/ct/">{{$item['name']}}</a></td>
      <td class="uk-table-link uk-link-reset"><a href="/ct/{{$item['name']}}">{{$item['description']}}</a></td>
      <td class="">{{$item['hostname']}}</td>
      <td>{{$item['ip_configured']}}</td>
      @if($item['status'] == 'running')
      <td class="uk-text-success uk-text-right">Running</td>
      @else
      <td class="uk-text-muted uk-text-right">Stopped</td>
      @endif
    </tr>
@endforeach
</table>

