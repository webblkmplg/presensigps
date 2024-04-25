<ul class="action-button-list">
    <li>
        {{-- <a href="#"  class="btn btn-list text-primary">
            <span>
                <ion-icon name="create-outline"></ion-icon>
                Edit
            </span>
        </a> --}}
        @if ($datacuti->status=="d")
        <a href="/dinasluar/{{$datacuti->kode_cuti}}/edit"  class="btn btn-list text-primary">
            <span>
                <ion-icon name="create-outline"></ion-icon>
                Edit
            </span>
        </a>
        @elseif($datacuti->status=="s")
        <a href="/cutisakit/{{$datacuti->kode_cuti}}/edit"  class="btn btn-list text-primary">
            <span>
                <ion-icon name="create-outline"></ion-icon>
                Edit
            </span>
        </a>
        @elseif($datacuti->status=="c")
        <a href="/izincuti/{{$datacuti->kode_cuti}}/edit"  class="btn btn-list text-primary">
            <span>
                <ion-icon name="create-outline"></ion-icon>
                Edit
            </span>
        </a>
        @endif
    </li>
    <li>
        <a href="#" id="deletebutton" class="btn btn-list text-danger" data-dismiss="modal" data-toggle="modal" data-target="#deleteConfirm">
            <span>
                <ion-icon name="trash-outline"></ion-icon>
                Delete
            </span>
        </a>
    </li>
</ul>

<script>
    $(function(){
        $("#deletebutton").click(function(e){
            $("#hapuspengajuan").attr('href','/cuti/' + '{{ $datacuti->kode_cuti }}/delete');
        });
    });
</script>