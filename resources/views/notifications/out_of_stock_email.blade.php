<p>Hi, <strong>{{ $data['name'] }}</strong></p>
<p>{{ $data['message'] }} with details below is out of stock : <br>
<strong>Product name:</strong> {{ $data['prod_name'] }}<br>
<strong>Quantity:</strong>  {{ $data['quantity'] }}<br>

</p>
<p>Kindly Login <a href="{{ route('dashboard') }}">here</a> to add new product.</p>