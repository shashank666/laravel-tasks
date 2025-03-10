<nav aria-label="Page navigation">
         <ul class="pagination">                             
                <!-- Previous Page Link -->
                @if ($paginator->onFirstPage())
                @else
                    <li class="page-item">
                    <a  class="page-link"  href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
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
                                <a class="page-link"  href="{{ $url }}">{{ $page }}<span class="sr-only">(current)</span></a>
                                </li>
                            @else
                                <li  class="page-item"><a class="page-link"  href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <!-- Next Page Link -->
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                        </a>
                    </li>    
                @endif
        </ul>
</nav>        