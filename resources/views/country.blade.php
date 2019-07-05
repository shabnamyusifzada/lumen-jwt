<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form method="post">
    <select class="form-control" name="parent_id">
        <option value="0">Choose coutry or city</option>
        @foreach($countries as $country)
            <option value="{{$country->id}}">{{$country->name}}</option>
            @foreach($country->child as $count)
                <option value="{{$count->id}}">{{$country->name}}-->{{$count->name}}</option>
                @foreach($count->child as $c)
                    <option value="{{$c->id}}">{{$country->name}}-->{{$count->name}}-->{{$c->name}}</option>
                @endforeach
            @endforeach
        @endforeach
    </select>
    <input type="text" name="name">
    <button type="submit">Add</button>
</form>
</body>
</html>