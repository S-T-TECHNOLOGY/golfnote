<!DOCTYPE html>
<html>
<head>
  <title>{{ $header }}</title>
</head>
<body>
<h3>{{ $header }}</h3>
<h4>{{ $content }}</h4>
<a href="{{ url('/confirm-email?code='.$code) }}" style="color: #0064aa">{{ url('/confirm-email?code='.$code) }}</a>

</body>
</html>
