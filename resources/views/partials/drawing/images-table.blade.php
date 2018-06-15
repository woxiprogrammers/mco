<center>
    <div id="myCarousel" class="carousel slide" data-ride="carousel" style="height: 200px;width: 500px">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <!-- Wrapper for slides -->
    <div class="carousel-inner ">
        @foreach($drawing_image_latest_version as $drawing_image_latest_version)
         @if($drawing_image_latest_version['iterator'] == 0)
                <div class="item active" style="width: 80%">
                    <a style="float: right" href="/drawing/images/get-details/{{$drawing_image_latest_version['id']}}">View Details</a>
                    <a href="{{$drawing_image_latest_version['encoded_name']}}" target="_blank">
                   <br>
                    <img src="{{$drawing_image_latest_version['encoded_name']}}" alt="" style="height: 150px">
                        <h4>{{$drawing_image_latest_version['title']}}</h4>
                    </a>
                </div>
         @else
                <div class="item" style="width: 80%">
                    <a style="float: right" href="/drawing/images/get-details/{{$drawing_image_latest_version['id']}}">View Details</a>
                    <a href="{{$drawing_image_latest_version['encoded_name']}}">
                    <br>
                    <img src="{{$drawing_image_latest_version['encoded_name']}}" alt="{{$drawing_image_latest_version['title']}}" style="height: 150px">
                        <h4>{{$drawing_image_latest_version['title']}}</h4>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
</center>