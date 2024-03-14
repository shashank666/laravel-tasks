<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border:0px;">
      <div class="modal-header" style="background-color:#244363;color:#fff;">
        <h5 class="modal-title mx-auto" id="searchModalTitle"><span style="vertical-align: bottom;margin-right:8px;">Search</span><img src="/img/logo-white.png" width="90" height="30" alt="Opined"/></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:0;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="GET" action="{{route('search')}}">
            {{ csrf_field() }}
          <input id="search" class="form-control form-control-lg" type="text" name="q" placeholder="Enter a keyword to search..." required/>
        </form>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
$('#searchModal').on('shown.bs.modal', function () {
    $('#search').focus();
});
});
</script>
@endpush
