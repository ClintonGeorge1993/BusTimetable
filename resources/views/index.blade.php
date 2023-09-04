
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <title>Bus Routes</title>
    <body>
        <div >
            <h3>Bus Route</h3>
            <div class="table-responsive " style="overflow-x:auto;">
            <table class="table table-borderless" >
                    <thead class="">
                    <?php
                    $temp_section = '';
                    $temp_section1 = '';
                    $temp_count1 = 0;
                    $column_count = 0;
                    $sscount = 1;
                    $section_count = 1 ?>
                    <th class="Active"><b>Route</b></th>

                    @foreach($dbdata as $data)
                        @if($temp_section != $data->route_section)
                            <?php
                                if ($temp_count1 > $column_count) {
                                    $column_count = $temp_count1;
                                    $temp_count1 = 0;
                                }
                                $temp_count1++;
                            ?>
                        @else
                               <?php $temp_count1++;?>
                        @endif
                         <?php $temp_section = $data->route_section?>
                    @endforeach
                    @for($i = 0;$i<$column_count;$i++ )
                        <th class="Active"><b>Start{{$sscount}}</b></th>
                        <th class="Active"><b>Stop{{$sscount}}</b></th>
                        <?php $sscount++;?>
                    @endfor
                    </thead>
                    @foreach($dbdata as $data)
                            @if($temp_section != $data->route_section)
                                </tr>
                                <tr>
                                    <td class="Active"><b>Route Section {{$section_count}}</b></td>
                                    <?php $section_count++ ?>
                                    <td>{{$data->from_stop}}</td>
                                    <td>{{$data->to_stop}}</td>
                            @else
                                <td>{{$data->from_stop}}</td>
                                <td>{{$data->to_stop}}</td>
                            @endif
                            <?php $temp_section = $data->route_section?>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    <style>
    </style>
    <script>
    </script>
</html>

