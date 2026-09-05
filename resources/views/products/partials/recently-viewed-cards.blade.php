@if ($products->isNotEmpty())
    @foreach ($products as $product)
        <x-product.product-card :product="$product" />
    @endforeach
@endif
