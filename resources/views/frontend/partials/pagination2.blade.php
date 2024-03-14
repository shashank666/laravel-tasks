<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">                             
           <!-- Previous Page Link -->
           @if ($paginator->onFirstPage())
           @else
               <li class="page-item">
               <a  class="page-btn page-link" href="javascript:void(0);" data-link="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
                </a>
               </li>
           @endif

           <!-- Pagination Elements -->
           @foreach ($elements as $element)
               <!-- "Three Dots" Separator -->
               @if (is_string($element))
                   <li class="page-item disabled"><span>{{ $element }}</span></li>
               @endif

               <!-- Array Of Links -->
               @if (is_array($element))
                   @foreach ($element as $page => $url)
                       @if ($page == $paginator->currentPage())
                           <li class="page-item active">
                           <a class="page-btn page-link"  href="javascript:void(0);"  data-link="{{ $url }}">{{ $page }}<span class="sr-only">(current)</span></a>
                           </li>
                       @else
                           <li class="page-item"><a class="page-btn page-link"  href="javascript:void(0);"  data-link="{{ $url }}">{{ $page }}</a></li>
                       @endif
                   @endforeach
               @endif
           @endforeach

           <!-- Next Page Link -->
           @if ($paginator->hasMorePages())
               <li class="page-item">
                   <a class="page-btn page-link" href="javascript:void(0);"  data-link="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                   <span aria-hidden="true">&raquo;</span>
                   <span class="sr-only">Next</span>
                   </a>
               </li>    
           @endif
   </ul>
</nav>        
<script>
    $(document).on('click','.page-btn',function(){
        if($(this).attr('data-link')!==null){
            var page=$(this).attr('data-link').split('?page=')[1];
            var newUrl=updateQueryStringParameter(window.location.href, 'page', page);
            window.location.href=newUrl;
        }
    });
</script>