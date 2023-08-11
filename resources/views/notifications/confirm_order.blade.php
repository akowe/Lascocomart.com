<p>Hi,  <strong>{{ $data['name'] }}</strong> your order is confirmed.</p>
<p>Order Details: <br>
<strong>Order number:</strong> {{ $data['order_number'] }}<br>
<strong>Amount:</strong>  {{ number_format($data['amount'])}}<br>
</p>

<p>Login <a href="{{ route('dashboard') }}">here</a> to see your order history.</p>
<p>Thank you for using LascocoMart.</p>

