<h2>Product Name: {{ $product->product_name }}</h2>
<p>Description: {{ $product->product_description }}</p>

<h3>Brands:</h3>
@foreach($product->brands as $brand)
    <p>Name: {{ $brand->brand_name }}</p>
    <p>Price: {{ $brand->price }}</p>
    <hr>
@endforeach

<h3>Total Price: {{ $total }}</h3>