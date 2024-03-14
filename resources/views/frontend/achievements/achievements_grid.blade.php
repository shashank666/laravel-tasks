<div
  class="col-4 col-lg-3 col-xl-2"
  id="{{ $achievement->achievement_id }}"
  data-toggle="modal"
  data-target="#achievement_{{ $achievement->achievement_id }}"
  style="cursor: pointer;"
>
  @if($flag==true)
    <div class="row" style="justify-content: center;">
        <img
          src="{{ $achievement->icon }}"
          alt="Medal"
          width="100"
        >
    </div>
    <div class="row mt-2 title" style="justify-content: center;">
        <h5>{{ $achievement->title }}</h5>
    </div>
  @else
    <div class="row" style="justify-content: center;">
        <img
          src="{{ $achievement->icon }}"
          alt="Medal"
          width="100"
          style="-webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */   filter: grayscale(100%);"
        >
    </div>
    <div class="row mt-2 title" style="justify-content: center;">
        <h5>{{ $achievement->title }}</h5>
    </div>
    @endif
</div>

<div
  class="modal fade"
  id="achievement_{{ $achievement->achievement_id }}"
  tabindex="-1"
  role="dialog"
  aria-labelledby="exampleModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="heading">{{ $achievement->title }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @if($flag==true)
        <div class="modal-body">
          <div class="row" style="justify-content: center;">
            <img
              src="{{ $achievement->icon }}"
              alt="Medal"
              width="200"
            >
          </div>
          <div class="row title mt-2" style="justify-content: center;">
            <p style="font-size: 1.5rem;">{{ $achievement->title }}</p>
          </div>
          <div class="row description" style="justify-content: center;">
            <p style="font-size: 1.3rem;">{{ $achievement->unlocked }}</p>
          </div>
        </div>
        <div class="modal-footer">
            Points rewarded : {{ $achievement->reward }}
        </div>
      @else
        <div class="modal-body">
          <p> {{ $achievement->locked }}</p>
        </div>
        <div class="modal-footer">
          Points to be rewarded : {{ $achievement->reward }}
        </div>
      @endif
    </div>
  </div>
</div>