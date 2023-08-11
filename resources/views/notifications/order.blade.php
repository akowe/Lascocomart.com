<p>Hi, You have new order from <strong>{{ $data['name'] }}</strong></p>
<p>Order Details: <br>
<strong>Order number:</strong> {{ $data['order_number'] }}<br>
<strong>Amount:</strong>  {{ number_format($data['amount'])}}<br>
</p>

<p><a href="{{ route('superadmin') }}">Login Here </a> to see your order history.</p>
<p>Thank you for using LascocoMart.</p>

