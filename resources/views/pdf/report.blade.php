
<html>
  <head>
      <base href="http://www.localhost.com:8000/">
      <style>
          table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
              padding: 5px;
          }
      
         .bar {
              fill: #aaa;
              height: 21px;
              transition: fill 0.3s ease;
              cursor: pointer;
              font-family: Helvetica, sans-serif;
          }
          .bar text {
              fill: #555;
          }
      
          .chart:hover .bar,
          .chart:focus .bar {
              fill: #aaa;
          }
      
          .bar:hover,
          .bar:focus {
          fill: red !important;
          }
          .bar:hover text,
          .bar:focus text {
          fill: red;
          }
      
          figcaption {
              font-weight: bold;
              color: #000;
              margin-bottom: 20px;
          }
      
          body {
           font-family: "Open Sans", sans-serif;
           }
           
                
      </style>
  </head>
  
  <body>
     
      
      <h1>{{$title}}</h1>
      
      <table style="width:50%">
          <tr>
              <td><strong>Jumlah Data</strong></td>
              <td>{{$n_data}} data</td>
          </tr>
          <tr>
              <td><strong>Jumlah Kategori</strong></td>
              <td>{{$n_categories}} kategori</td>
          </tr>
          <tr>
              <td><strong>Data Terakhir Masuk</strong></td>
              <td>{{$last_data}}</td>
          </tr>
          <tr>
              <td><strong>Kategori Terbanyak</strong></td>
              <td>{{$most_category}}</td>
          </tr>
      </table>
      
      <div>
          <h3>Jumlah data Masuk</h3>
          <figure>
              <figcaption>Jumlah data masuk per kategori</figcaption>
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="chart" width="820" height="250" aria-labelledby="title" role="img">
              <title id="title">A bart chart showing information</title>
             
              <?php $y = 0;?>
              <?php $y_text=8; ?>
              
              @for($i = 0 ;$i < sizeof($freq["labels"]);$i++)
                  <g class="bar">
                  <rect width="{{$freq['values'][$i]/10}}" height="19" y="{{$y}}"></rect>
                  <text x="{{$freq['values'][$i]/10+5}}" y="{{$y_text}}" dy=".35em">{{$freq['values'][$i]}} {{$freq['labels'][$i]}}</text>
                   </g>
                <?php $y = $y + 20?>
                <?php $y_text = $y_text + 20?>
              @endfor
            </svg>
            </figure>
      </div>
      <br><br>
      <div>
          <h3>Distribusi Kategori</h3>
          <!-- <img src="{{public_path()}}/chart/pie.jpg" alt=""> -->
          <img src="/var/www/html/tugas_akhir/ta/public/chart/pie.jpg" alt="">
      </div>  
      <br><br><br><br><br><br><br><br><br><br><br><br>
      <div>
          <h3>Data masuk per bulan</h3>  
          <table style="width:100%">
             <tr>
                <th>Kategori</th>
                @foreach($periodes as $periode)
                 <th>{{$periode->DATE}}</th>
                @endforeach
              </tr>
              <tbody>
                  @for($i =0;$i <= $n_categories ;$i++)
                  <tr>
                    <td>{{$monthly_freqs[$i*$n_categories-$i]->predicted}}</td>
                    @for($j =0;$j < sizeof($periodes) ;$j++)
                        <td>{{$monthly_freqs[$i*$n_categories+$j-$i]->jumlah}}</td>
                    @endfor
                  </tr>
                  @endfor
              </tbody>
          </table>
      </div>
      <br><br>
      <div>
          <h3>Data Masuk</h3>
          <table style="width:100%">
              <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Kategori</th>
                <th>Tanggal</th>
              </tr>
              <tbody>
                @foreach($tweets as $tweet)
                <tr>
                    <td>{{$tweet->id}}</td>
                    <td>{{$tweet->tweet}}</td>
                    <td>{{$tweet->predicted}}</td>
                    <td>{{$tweet->date}}</td>
                </tr>
                @endforeach
              </tbody>
          </table>
      </div>    
      
  </body>
  <script></script>
</html>