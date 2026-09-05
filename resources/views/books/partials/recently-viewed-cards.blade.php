@if ($books->isNotEmpty())
    @foreach ($books as $book)
        <x-product.product-card :book="$book" />
    @endforeach
@endif
