<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Excel;
Use App\Store_list;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     public function login()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('auth.login',['wallpaper'=>$wallpaper]);
     }
     public function setting()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

        return view('setting',['wallpaper'=>$wallpaper]);
     }
     public function admindashboard()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('admindashboard',['wallpaper'=>$wallpaper]);
     }
     public function newstore() //edited by Firdaus 02/04/2018
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();
       $Store = DB::table('store_list')->get();
         return view('newstore',['wallpaper'=>$wallpaper],['Store'=>$Store]);
     }
     public function addstore(Request $request)
     {
       $input = $request->all();

        $id = DB::table('store_list')->insertGetId(
           [
             'Store' => $input["store_id"],
             'Name' => $input["store_name"],
           ]
         );

         $id2 = DB::table('users')->insertGetId(
            [
              'store_id' => $input["store_id"],
              'name' => $input["store_name"],
              'email' => $input["store_email"],
              'password' => bcrypt($input["store_password"]),
              'usertype' => $input["usertype"],
            ]
          );



         $wallpaper = DB::table('image')
         ->select('image.*')
         ->first();
         return view('addstoremessage',['wallpaper'=>$wallpaper]);
     }

     public function displaystore($id)
     {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $data=DB::table('store_list')
        ->where('Store',$id)
        ->first();

        return view('editstore',['data'=>$data,'wallpaper'=>$wallpaper]);
     }

     public function editstore(Request $request)
     {
        $input=$request->all();

        DB::table('store_list')
        ->where('Store',$input['id'])
        ->update(['Store'=>$input['store_id'],'Name'=>$input['store_name']]);
        DB::table('users')
        ->where('store_id',$input['id'])
        ->update(['store_id'=>$input['store_id']]);
        return $this->newstore();
     }
     public function availablesummary()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('availablesummary',['wallpaper'=>$wallpaper]);
     }
     public function newuser() //edited by Firdaus 02/04/2018
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();
       $users = DB::table('users')->get();
         return view('newuser',['wallpaper'=>$wallpaper],['users'=>$users]);
     }
     public function adduser(Request $request )
     {
       $input = $request->all();

         $id = DB::table('users')->insertGetId(
           [
             'name' => $input["username"],
             'email' => $input["useremail"],
             'password' => bcrypt($input["password"]),
             'usertype' => $input["usertype"],

           ]
         );

         $wallpaper = DB::table('image')
         ->select('image.*')
         ->first();

         return view('addusermessage',['wallpaper'=>$wallpaper]);
     }
     public function displayuser($id)
     {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        $display=DB::table('users')
        ->where('id',$id)
        ->first();
        return view('edituser',['display'=>$display,'wallpaper'=>$wallpaper]);
     }
     public function edituser(Request $request)
     {  $input=$request->all();
        $update=DB::table('users')
        ->where('id',$input['id'])
        ->update(['name'=>$input['username'],'email'=>$input['useremail']]);
        return $this->newuser();
     }
     public function deleteuser($id,$store_id)
     {

         DB::table('users')
         ->where('id',$id)
         ->delete();
        if($store_id != 0)
        {
         DB::table('store_list')
         ->where('Store',$store_id)
         ->delete();
        }
         return back();
     }
     public function addusermessage()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('addusermessage',['wallpaper'=>$wallpaper]);
     }
     public function addstoremessage()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('addstoremessage',['wallpaper'=>$wallpaper]);
     }
     public function stock()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();
       $user = Auth::user();
       $stock = DB::table('stocks')
           ->join('users', 'users.store_id', '=', 'stocks.store_id')
           // ->where('users.store_id','=',$user->store_id)
           ->select('stocks.DCS_Code', 'stocks.UPC', 'stocks.ALU', 'stocks.Name', 'stocks.Quantity', 'stocks.create_dt', 'stocks.File_Name', 'stocks.Web_Path', 'users.store_id')
           // ->groupby('DCS_Code')
           ->orderby('stocks.create_dt','DESC')
           // ->take('20')
           ->get();
       $users = DB::table('users')
           ->where('id','=',$user->id)
           ->select('store_id')
           ->get();
         return view('stock',['wallpaper'=>$wallpaper], ['stock'=>$stock])->with(['users'=>$users]);
     }
     public function newstock(Request $request)
     {
       $input = $request->all();
       $user = Auth::user();
       $type="User";
       $uploadcount=1;

         $file = $request->file('stock_img');
         $destinationPath=public_path()."/private/upload/User";
         $extension = $file->getClientOriginalExtension();
         $originalName=$file->getClientOriginalName();
         $fileSize=$file->getSize();
         $fileName=time()."_".$uploadcount.".".$extension;
         $upload_success = $file->move($destinationPath, $fileName);

         $id = DB::table('stocks')->insertGetId(
           [
             'DCS_Code' => $input["DCS_Code"],
             'UPC' => $input["UPC"],
             'ALU' => $input["ALU"],
             'Name' => $input["itemname"],
             'Quantity' => $input["quantity"],
             'store_id' => $input["store_id"],
             // 'create_dt' => Carbon::now(),
             'Type' => $type,
             'File_Name' => $originalName,
             'File_Size' => $fileSize,
             'Web_Path' => '/private/upload/User/'.$fileName
           ]
         );

         $wallpaper = DB::table('image')
         ->select('image.*')
         ->first();

         $stock = DB::table('stocks')
             ->join('users', 'users.store_id', '=', 'stocks.store_id')
             // ->where('users.store_id','=',$user->store_id)
             ->select('stocks.DCS_Code', 'stocks.UPC', 'stocks.ALU', 'stocks.Name', 'stocks.Quantity', 'stocks.create_dt', 'stocks.File_Name', 'stocks.Web_Path', 'users.store_id')

             // ->groupby('DCS_Code')
             ->orderby('stocks.create_dt','DESC')
             // ->take('20')
             ->get();
         $users = DB::table('users')
             ->where('id','=',$user->id)
             ->select('store_id')
             ->get();

         return view('stock',['wallpaper'=>$wallpaper], ['stock'=>$stock])->with(['users'=>$users]);

     }
     public function displaystock($id)
     {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

         $data=DB::table('stocks')
         ->where('ALU',$id)
         ->first();
         return view('editstock',['wallpaper'=>$wallpaper,'data'=>$data]);
     }
     public function editstock(Request $request)
     {
        $input=$request->all();
        $uploadcount=1;
        $file = $request->file('stock_img');
        if($file != '')
        {
        $destinationPath=public_path()."/private/upload/User";
        $extension = $file->getClientOriginalExtension();
        $originalName=$file->getClientOriginalName();
        $fileName=time()."_".$uploadcount.".".$extension;
        $upload_success = $file->move($destinationPath, $fileName);
        $update=DB::table('stocks')
        ->where('ALU',$input['ALU'])
        ->update(['DCS_CODE'=>$input['DCS_Code'],'Name'=>$input['itemname'],'Quantity'=>$input['quantity'],'UPC'=>$input['UPC'],'ALU'=>$input['ALU'],'Web_Path'=>'/private/upload/User/'.$fileName]);
        }
        else
        {
            $update=DB::table('stocks')
            ->where('ALU',$input['ALU'])
            ->update(['DCS_CODE'=>$input['DCS_Code'],'Name'=>$input['itemname'],'Quantity'=>$input['quantity'],'UPC'=>$input['UPC'],'ALU'=>$input['ALU']]);
        }
         return $this->stock();
     }
     public function deletestock($id)
     {
         $delete=DB::table('stocks')
         ->where('ALU',$id)
         ->delete();
         return $this->stock();
     }
     public function changewallpaper()
     {
       $wallpaper = DB::table('image')
       ->select('image.*')
       ->first();

         return view('changewallpaper',['wallpaper'=>$wallpaper]);
     }
     public function savewallpaper(Request $request)
     {
        $input = $request->all();
        $type="User";
        $uploadcount=1;
        //$file = Input::file('profilepicture');
        if ($request->hasFile('wallpaper')) {
            $file = $request->file('wallpaper');
            $destinationPath=public_path()."/private/upload/User";
            $extension = $file->getClientOriginalExtension();
            $originalName=$file->getClientOriginalName();
            $fileSize=$file->getSize();
            $fileName=time()."_".$uploadcount.".".$extension;
            $upload_success = $file->move($destinationPath, $fileName);
            DB::table('image')->update(
                [
                    'Type' => $type,
                    'File_Name' => $originalName,
                    'File_Size' => $fileSize,
                    'Web_Path' => '/private/upload/User/'.$fileName
                ]
            );

        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

                return view('savewallpapermessage',['wallpaper'=>$wallpaper]);
            }
            else {
                return 0;
            }
    	}
      public function savewallpapermessage()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        return view('savewallpapermessage',['wallpaper'=>$wallpaper]);
      }
      public function import(){

        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        return view ('import',['wallpaper'=>$wallpaper]);

      }
      public function importedfiles(){

        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        return view ('importedfiles',['wallpaper'=>$wallpaper]);

      }

      public function importExport()
      {
          return view('importExport');
      }

      // Firdaus - 21/3/2018
      // public function downloadExcel($type)
      // {
      //     $data = Item::get()->toArray();
      //     return Excel::create('itsolutionstuff_example', function($excel) use ($data)
      //     {
      //           $excel->sheet('mySheet', function($sheet) use ($data)
      //           {
      //             $sheet->fromArray($data);
      //           });
      //     })->download($type);
      // }

      public function importExcelCustomerList(Request $request)
      {
      if(Input::hasFile('import_file')){
        // if(Input::get('import_file_name') == 'customer_list'){
          {
            $upload=$request->file('import_file');
            $filePath=$upload->getRealPath();

            $row=0;
            $skip = 1;
            $lastrow=false;
            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                for ($i = 0; $i < $skip; $i++)
                {
                    fgets($handle);
                }

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                {   $create_dt=str_replace('"','',$data[15]);
                    $insert[]=[
                                 'Str_Code' => $data[0],
                                 'Cust_ID' => $data[1],
                                 'Last' => str_replace('"','',$data[2]),
                                 'First' => str_replace('"','',$data[3]),
                                 'Phone1' =>$data[4],
                                 'Info1' => floatval($data[5]),
                                 'Last_Sale_Dt' => $data[6],
                                 'Category' => $data[7],
                                 'Prc_Lvl' =>$data[8],
                                 'Name' => str_replace('"','',$data[9]),
                                 'Total_Unit' => $data[10],
                                 'Total_Sale' => $data[11],
                                 'Total_Trans' => $data[12],
                                 'Visits' => $data[13],
                                 'Created_By' => $data[14],
                                 'Create_Dt' => date('Y-m-d H:i:s',strtotime(str_replace('/', '-',$create_dt))),
                                 'Race' => $data[16],
                                 'Region' => $data[17],
                                 'Email' => str_replace('"','',$data[18])

                    ];
                    //date('Y-m-d',strtotime(str_replace('/', '-',$data[3])))
                    $row++;
                    if($row == 900)
                    {
                        DB::table('customer_list')->insert($insert);
                        unset($insert);
                    }
                }
                if(DB::table('customer_list')->insert($insert))
                  dd('Insert Record successfully.');
            }

            }
          }
      }


      public function importExcelCustomerSales(Request $request)
      {
          //if(Input::get('import_file_option') == 'customer_sales')
          {
          //get file
          $upload=$request->file('import_file');
          $filePath=$upload->getRealPath();
          $path = Input::file('import_file')->getRealPath();
          $data = Excel::load($path, function($reader)
            {
            })
            ->get();
            $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE customer_sales
            FIELDS TERMINATED BY ','
            OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 3 LINES
            (Store_Name ,Customer_Name,Email_Addr,ALU,Item_Name,DCS_Code,INVC_No,Qty_Sold,Orig_Price,Sales,Disc,Price,Orig_Tax)",addslashes($path));
            $insert[]=[
                    'Store_Name' =>  $data[0],
                    'Customer_Name' => $data[1],
                    'Email_Addr' => $data[2],
                    'ALU' => $data[3],
                    'Item_Name' => $data[4],
                    'DCS_Code' => $data[5],
                    'INVC_No' => $data[6],
                    'Qty_Sold' => $data[7],
                    'Orig_Price' => $data[8],
                    'Sales' => $data[9],
                    'Disc' => $data[10],
                    'Price' => $data[11],
                    'Orig_Tax' => $data[12]
            ];
            if (DB::connection()->getpdo()->exec($query))
            {
                DB::table("customer_sales")->where("Customer_Name", "Total")->delete();
                dd('Insert Record successfully.');
            }
            /*
            if(!empty($data) && $data->count())
                {
                    foreach ($data as $key => $value)
                    {   //echo $value;
                        $insert[] = [
                            'Store_Name' =>  $value ->store_name,
                            'Customer_Name' => $value ->customer_name,
                            'Email_Addr' => $value ->email_addr,
                            'ALU' => $value ->alu,
                            'Item_Name' => $value ->itemname,
                            'DCS_Code' => $value ->dcs_code,
                            'INVC_No' => $value ->invc_no,
                            'Qty_Sold' => $value ->qty_sold,
                            'Orig_Price' => $value ->orig_price,
                            'Sales' => $value ->sales,
                            'Disc' => $value ->disc,
                            'Price' => $value ->price,
                            'Orig_Tax' => $value ->orig_tax
                                    ];
                    }/*
          //skip header (2 rows)
          $row = 1;
          $skip = 3;
          if (($handle = fopen($filePath, 'r')) !== FALSE) {
              for ($i = 0; $i < $skip; $i++) {
                  fgets($handle);
              }

              while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                  $num = count($data);
                  $row++;
                  $insert[]  = [

                  ];
                  unset($data);
              }*/

           /*   if(!empty($insert))
                    {
                        DB::table('customer_sales')->insert($insert);
                        DB::connection()->disableQueryLog();
                        dd('Insert Record successfully.');
                    }
                    fclose($handle); */
          //}
        }
         return back();

      }


      public function importExcelSalesItemSummary()
      {
          if(Input::hasFile('import_file'))
          {
                $path = Input::file('import_file')->getRealPath();
                $data = Excel::load($path, function($reader)
                {

                })->get();
                $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE sales_item_summary
                         FIELDS TERMINATED BY ','
                         OPTIONALLY ENCLOSED BY '\"'
                         LINES TERMINATED BY '\r\n'
                         IGNORE 6 LINES
                         (DCS_Code, D, C, S, Description1, @dummy, @dummy, @dummy, ALU, UPC, @dummy, Qty_Sold, Disc, @dummy, Disc_Amt, @dummy, Ext_P, @dummy, Ext_PT)",addslashes($path));
                DB::connection()->getpdo()->exec($query);
                /*
                if(!empty($data) && $data->count())
                {
                    foreach ($data as $key => $value)
                    {
                        $insert[] = [
                                                          // 'DCS_Code' => $value->{'DCS Code'},
                                                          // 'D' => $value->D,
                                                          // 'C' => $value->C,
                                                          // 'S' => $value->S,
                                                          // 'Description1' => $value->Description1,
                                                          // 'ALU' => $value->ALU,
                                                          // 'UPC' => $value->UPC,
                                                          // 'Qty_Sold' => $value->{'Qty Sold'},
                                                          // 'Disc' => $value->{'Disc %'},
                                                          // 'Disc_Amt' => $value->{'Disc Amt'},
                                                          // 'Ext_P' => $value->{'Ext P$'},
                                                          // 'Ext_PT' => $value->{'Ext P$T$'}

                                                          'DCS_Code' => $data[0],
                                                          'D' => $value->$data[1],
                                                          'C' => $value->c,
                                                          'S' => $value->s,
                                                          'Description1' => $value->description1,
                                                          'ALU' => $value->alu,
                                                          'UPC' => $value->upc,
                                                          'Qty_Sold' => $value->qty_sold,
                                                          'Disc' => $value->disc,
                                                          'Disc_Amt' => $value->disc_amt,
                                                          'Ext_P' => $value->ext_p,
                                                          'Ext_PT' => $value->ext_pt
                                    ];
                    }
                    if(!empty($insert))
                    {
                        DB::table('sales_item_summary')->insert($insert);
                        dd('Insert Record successfully.');
                    }
                }*/
            }
            return back();
      }

      public function importExcelSalesReceiptSummary()
      {
        if(Input::hasFile('import_file'))
        $path = Input::file('import_file')->getRealPath();
        $path=addslashes($path);
        $query = "LOAD DATA LOCAL INFILE '$path' INTO TABLE sales_receipt_summary
                  FIELDS TERMINATED BY ','
                  OPTIONALLY ENCLOSED BY '\"'
                  LINES TERMINATED BY '\r\n'
                  IGNORE 5 LINES
                  (Customer_Name, @dummy, @dummy, Store, Rcpt, @Rcpt_date, @Rcpt_time, Tender_Name, @dummy, Ext_Orig_Price, @dummy, Ext_Disc, Ext_Disc_Amt, Ext_P, @dummy, Ext_PT, Rcpt_Tax_Amt, @dummy, Rcpt_Total)
                  SET Rcpt_Date_Time=concat(STR_TO_DATE(@Rcpt_date, '%d/%m/%Y'), ' ', @Rcpt_time)";

       if(DB::connection()->getpdo()->exec($query))
          dd('Insert Record successfully.');
       // return back();
      }

      public function importExcelSalesReceiptData(Request $request)
      {
        $path = Input::file('import_file')->getRealPath();
        $upload=$request->file('import_file');
        $filePath=$upload->getRealPath();
        $row = 0;
        $count=0;
        $skip = 7;
        $lastrow=0;
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            for ($i = 0; $i < $skip; $i++)
            {
                fgets($handle);
            }

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
            {
                $num = count($data);
                foreach ($data as $key => $value)
                {
                    $count++;
                    if ($value == "")
                       $count-=1;
                    if(!($count > 0))
                       $lastrow=1;

                }

                if ($count == 13)
                {
                    $obj=[
                                'Customer' => $data[0],
                                'Store' => $data[1],
                                'Rcpt' => $data[2],
                                'Rcpt_Date' => date('Y-m-d',strtotime(str_replace('/', '-',$data[3]))),
                                'Rcpt_Date_Time' => $data[4],
                                'Tender_Name' => $data[5],
                                'Ext_Orig_Price' => $data[6],
                                'Disc' => $data[7],
                                'Ext_Disc_Amt' => $data[8],
                                'Ext_P' => $data[9],
                                'Ext_PT' => $data[10],
                                'Rcpt_Tax_Amt' => $data[11],
                                'Rcpt_Amt' => $data[12],
                    ];
                }
                if($count == 8)
                {
                    $insert[]=[
                        'Customer' => $obj["Customer"],
                        'Store' => $obj["Store"],
                        'Rcpt' => $obj["Rcpt"],
                        'Rcpt_Date' => $obj["Rcpt_Date"],
                        'Rcpt_Date_Time' => $obj["Rcpt_Date_Time"],
                        'Tender_Name' => $obj["Tender_Name"],
                        'Ext_Orig_Price' => $obj["Ext_Orig_Price"],
                        'Disc' => $obj["Disc"],
                        'Ext_Disc_Amt' => $obj["Ext_Disc_Amt"],
                        'Ext_P' => $obj["Ext_P"],
                        'Ext_PT' => $obj["Ext_PT"],
                        'Rcpt_Tax_Amt' => $obj["Rcpt_Tax_Amt"],
                        'Rcpt_Amt' => $obj["Rcpt_Amt"],
                        'UPC' => $data[0],
                        'ALU' => $data[1],
                        'DCS' => $data[2],
                        'Size' => $data[3],
                        'Desc1' => $data[4],
                        'Sold_Qty' => $data[5],
                        'P' => $data[6],
                        'PT' => $data[7],
                        'Cost' => $data[8]
                    ];
                }

                $count =0;
                $row++;
                unset($data);
                if($row == 900 || $lastrow == 1)
                {
                    if(!empty($insert))
                    {
                        DB::table('sales_receipt_data')->insert($insert);
                        //dd('Insert Record successfully.');
                    }
                    $row=0;
                    unset($insert);
                }

            }
        }
        dd('Insert Record successfully.');
      }
      public function importExcelStoreList(Request $request)
      {
          if(Input::get('import_file') == 'ca'){

          //get file
          $upload=$request->file('import_file');
          $filePath=$upload->getRealPath();

          //open and read
          //$file=fopen($filePath, 'r');
          //$header = fgetcsv($file);

          //skip header (6 rows)
          $row = 1;
          $skip = 6;
          if (($handle = fopen($filePath, 'r')) !== FALSE) {
              for ($i = 0; $i < $skip; $i++) {
                  fgets($handle);
              }

              while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                  $num = count($data);
                  $row++;
                  /*for ($c=0; $c < $num; $c++) {
                      //echo $data[$c];
                  }*/
                      $insert[]  = [
                                          'Store' =>  $data[0],
                                          'Name'  =>  $data[1]

                                   ];
              }
              if(!empty($insert))
                    {
                        DB::table('store_list')->insert($insert);
                        dd('Insert Record successfully.');
                    }

              //fclose($handle);
          }
        }
         // return back();

      }

      public function importExcelItem()
      {
          if(Input::hasFile('import_file'))
          {
                $path = Input::file('import_file')->getRealPath();
                $data = Excel::load($path, function($reader)
                {

                })->get();
                if(!empty($data) && $data->count())
                {
                    foreach ($data as $key => $value)
                    {
                        $insert[] = [
                                                          'ids' => $value->id,
                                                          'titles' => $value->title,
                                                          'descriptions' => $value->descriptions,
                                                          'store' => $value->store,
                                                          'name' => $value->name
                                    ];
                    }
                    if(!empty($insert))
                    {
                        DB::table('item')->insert($insert);
                        dd('Insert Record successfully.');
                    }
                }
            }
            return back();
      }

      // Import csv for Customer List
      // public function importExcelCustomerList()
      // {
      //     if(Input::hasFile('import_file'))
      //     {
      //           $path = Input::file('import_file')->getRealPath();
      //           $data = Excel::load($path, function($reader)
      //           {
      //
      //           })->get();
      //           if(!empty($data) && $data->count())
      //           {
      //               foreach ($data as $key => $value)
      //               {
      //                   $insert[] = [ 'Str_Code' => $value->Str_Code,
      //                                 'Cust_ID' => $value->Cust_ID
      //                                 // 'Last' => $value->Last,
      //                                 // 'First' => $value->First,
      //                                 // 'Phone1' => $value->Phone1,
      //                                 // 'Info1' => $value->Info1,
      //                                 // 'Last_Sale_Dt' => $value->Last_Sale_Dt,
      //                                 // 'Category' => $value->Category,
      //                                 // 'Prc_Lvl' => $value->Prc_Lvl,
      //                                 // 'Name' => $value->Name,
      //                                 // 'Total_Units' => $value->Total_Units,
      //                                 // 'Total_Sales' => $value->Total_Sales,
      //                                 // 'Total_Trans' => $value->Total_Trans,
      //                                 // 'Visits' => $value->Visits,
      //                                 // 'Created_By' => $value->Created_By,
      //                                 // 'Create_Dt' => $value->Create_Dt,
      //                                 // 'Race' => $value->Race,
      //                                 // 'Region' => $value->Region,
      //                                 // 'Email' => $value->Email
      //                               ];
      //               }
      //               if(!empty($insert))
      //               {
      //                   DB::table('customer_list')->insert($insert);
      //                   dd('Insert Record successfully.');
      //               }
      //           }
      //       }
      //       return back();
      // }

      // Import csv for Total Sales Transaction Record
       public function importExcelTotalSalesTransactionRecord()
       {
           if(Input::hasFile('import_file'))
           {
                 $path = Input::file('import_file')->getRealPath();
                 $data = Excel::load($path, function($reader)
                 {

                 })->get();

                $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE total_sales_transaction
                         FIELDS TERMINATED BY ','
                         OPTIONALLY ENCLOSED BY '\"'
                         LINES TERMINATED BY '\r\n'
                         IGNORE 1 LINES
                         (Store_Name, Customer_Name, INVC_No, Rolling_Month, DCS_Code, ALU, Item_Name, Year, Qty_Sold, Orig_Price, Sales, Disc, Price, Orig_Tax)",addslashes($path));

                if(DB::connection()->getpdo()->exec($query))
                {      DB::connection()->disableQueryLog();
                       DB::table("total_sales_transaction")->where("Customer_Name", "Total")->delete();
                       dd('Insert Record successfully.');
                }
                 /*
                 if(!empty($data) && $data->count())
                 {
                     foreach ($data as $key => $value)
                     {
                         $insert[] = [ 'STORE_NAME' => $value->STORE_NAME,
                                       'CUSTOMER_NAME' => $value->CUSTOMER_NAME,
                                       'INVC_NO' => $value->INVC_NO,
                                       'RollingMonth' => $value->RollingMonth,
                                       'DCS_CODE' => $value->DCS_CODE,
                                       'ALU' => $value->ALU,
                                       'ITEMNAME' => $value->ITEMNAME,
                                       'Year' => $value->Year,
                                       'Qty Sold' => $value->Qty_Sold,
                                       'Orig Price' => $value->Orig_Price,
                                       'Sales' => $value->Sales,
                                       'Disc %' => $value->Disc,
                                       'Price' => $value->Price,
                                       'Orig Tax' => $value->Orig_Tax
                                     ];
                     }
                     if(!empty($insert))
                     {
                         DB::table('total_sales_transaction')->insert($insert);
                         dd('Insert Record successfully.');
                     }
                 } */
             }
      //       return back();
       }
      // Firdaus - 21/3/2018

      public function importExcel(Request $request)
      {
        if(Input::get('import_file_option') == 'customer_list')
            return $this->importExcelCustomerList($request);
        else if(Input::get('import_file_option') == 'customer_sales')
            return $this->importExcelCustomerSales($request);
        else if(Input::get('import_file_option') == 'sales_item_summary')
            return $this->importExcelSalesItemSummary();
        else if(Input::get('import_file_option') == 'sales_receipt_data')
            return $this->importExcelSalesReceiptData($request);
        else if(Input::get('import_file_option') == 'sales_receipt_summary')
            return $this->importExcelSalesReceiptSummary();
        else if(Input::get('import_file_option') == 'total_sales_transaction_record')
            return $this->importExcelTotalSalesTransactionRecord();
      }
      public function catredeemsummary()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
          return view('catredeemsummary',['wallpaper'=>$wallpaper]);
      }
      public function catsalessummary()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
          return view('catsalessummary',['wallpaper'=>$wallpaper]);
      }
      //Redemption Summary
      public function summaryRQtyByOutlet()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $redemption1 = DB::table('redemption')
            ->join('store_list', 'redemption.store', '=', 'store_list.Store')
            ->select('redemption.*','store_list.Store','store_list.Name',DB::raw('COUNT(redemption.redemption_id) AS total_quantity'))
            ->groupby('store_list.Name')
            ->orderby('store_list.Store')
            ->get();
          return view('summaryRQtyByOutlet',['wallpaper'=>$wallpaper],['redemption1'=>$redemption1]);
      }
      public function summaryRCustomerExtNew()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $redemption2 = DB::table('redemption')
            ->join('store_list', 'redemption.store', '=', 'store_list.Store')
            ->select('redemption.*','store_list.Store','store_list.Name',DB::raw('COUNT(redemption.redemption_id) AS total_quantity'))
            ->groupby('store_list.Name')
            ->orderby('store_list.Store')
            ->get();
          return view('summaryRCustomerExtNew',['wallpaper'=>$wallpaper],['redemption2'=>$redemption2]);
      }
      public function summaryRQtyItemReport()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        // $redemption3 = DB::table('redemption')
        //     ->join('store_list', 'redemption.store', '=', 'store_list.Store')
        //     ->select('redemption.*','store_list.Store','store_list.Name',DB::raw('COUNT(redemption.redemption_id) AS total_quantity'))
        //     ->groupby('store_list.Name')
        //     ->orderby('store_list.Store')
        //     ->get();
        $redemption3 = DB::table('stocks')
        ->select('DCS_Code','ALU', 'Name', DB::raw('SUM(Quantity) AS Quantity'))
        ->orderby('Name', 'ASC')
        ->groupby('ALU','Quantity')
        ->get();

          return view('summaryRQtyItemReport',['wallpaper'=>$wallpaper],['redemption3'=>$redemption3]);
      }
      public function summaryRCustomerDetail()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        // $redemption4 = DB::table('redemption')
        //     ->rightjoin('customer_list', 'redemption.customer_id', '=', 'customer_list.Cust_ID')
        //     ->select('redemption.*','customer_list.Name','customer_list.DOB','customer_list.Category','customer_list.Region','customer_list.Race','customer_list.Email')
        //     ->orderby('redemption.redemption_id')
        //     ->get();

        $redemption4 = DB::table('redemption')
            ->join('customer_list', 'redemption.customer_id', '=', 'customer_list.Cust_ID')
            ->select('redemption.*','customer_list.Name','customer_list.DOB','customer_list.Category','customer_list.Region','customer_list.Race','customer_list.Email')
            ->orderby('redemption.redemption_id', 'customer_list.name', 'ASC')
            ->get();

          return view('summaryRCustomerDetail',['wallpaper'=>$wallpaper],['redemption4'=>$redemption4]);
      }

      public function summaryRPeriodEvent()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $redemption5 = DB::table('redemption')
            ->join('store_list', 'redemption.store', '=', 'store_list.Store')
            ->select('redemption.*','store_list.Store','store_list.Name',DB::raw('COUNT(redemption.redemption_id) AS total_quantity'))
            ->groupby('store_list.Name')
            ->orderby('store_list.Store')
            ->get();
          return view('summaryRPeriodEvent',['wallpaper'=>$wallpaper],['redemption5'=>$redemption5]);
      }
      public function summaryRHistory()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $redemption6 = DB::table('redemption')
            ->rightjoin('customer_list', 'redemption.customer_id', '=', 'customer_list.Cust_ID')
            ->select('redemption.*','customer_list.Name','customer_list.DOB','customer_list.Total_Unit','customer_list.Visits','customer_list.Total_Sale')
            ->orderby('customer_list.Name')
            ->get();
          return view('summaryRHistory',['wallpaper'=>$wallpaper],['redemption6'=>$redemption6]);
      }
      //Sales Summary
      public function summarySItemPerOutlet()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();

        $sales1 = DB::table('sales_receipt_data')
            ->join('store_list', 'sales_receipt_data.Store', '=', 'store_list.Store')
            ->join('sales_item_summary', 'sales_item_summary.ALU', '=', 'sales_receipt_data.ALU')
            ->select('store_list.Name', 'store_list.Store', 'sales_receipt_data.DCS', 'sales_receipt_data.ALU', 'sales_receipt_data.Desc1',
              DB::raw('SUM(sales_receipt_data.Sold_Qty) AS Sold_Qty'), 'sales_item_summary.Ext_PT', DB::raw('TRUNCATE(SUM(sales_item_summary.Ext_P), 2) AS P'),
              DB::raw('TRUNCATE(SUM(sales_item_summary.Ext_PT), 2) AS PT'))
            ->orderby('store_list.Store','ASC')
            ->get();

          return view('summarySItemPerOutlet',['wallpaper'=>$wallpaper], ['sales1'=>$sales1]);
      }
      public function summarySReceiptPerOutlet()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $sales2 = DB::table('sales_receipt_summary')
            ->rightjoin('store_list', 'sales_receipt_summary.Store', '=', 'store_list.Store')
            ->select('sales_receipt_summary.*','store_list.Name')
            ->orderby('store_list.Store','ASC')
            ->get();
          return view('summarySReceiptPerOutlet',['wallpaper'=>$wallpaper],['sales2'=>$sales2]);
      }
      public function summarySPurchaseHistory()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
            $sales3 = DB::table('total_sales_transaction')
                ->select('Customer_Name','INVC_NO','Rolling_Month','Qty_Sold','Orig_Price','Sales','Disc','Price','Orig_Tax',DB::raw('(Sales - Orig_Tax) AS before_gst'))
                ->groupby('Customer_Name')
                ->orderby('Customer_Name','ASC')
                ->get();
          return view('summarySPurchaseHistory',['wallpaper'=>$wallpaper],['sales3'=>$sales3]);
      }
      public function summarySLocalVSForeigner()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $sales4 = DB::table('customer_list')
                ->join('store_list', 'customer_list.Str_Code', '=', 'store_list.Store')
                ->select('store_list.*',DB::raw('COUNT(CASE WHEN customer_list.Region = "Malaysia" THEN 1 ELSE NULL END) as "Local"'),DB::raw('COUNT(CASE WHEN customer_list.Region != "Malaysia" THEN 1 ELSE NULL END) as "Foreigner"'))
                ->groupby('store_list.Name')
                ->orderby('store_list.Store', 'ASC')
                ->get();
          return view('summarySLocalVSForeigner',['wallpaper'=>$wallpaper],['sales4'=>$sales4]);
      }
      public function summarySTopSellingItem()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $sales5 = DB::table('sales_item_summary')
                ->select('Description1', 'Qty_Sold')
                ->orderby(DB::raw('CAST(Qty_Sold AS INTEGER)'), 'DESC')
                ->take('20')
                ->get();
          return view('summarySTopSellingItem',['wallpaper'=>$wallpaper], ['sales5'=>$sales5]);
      }
      public function summarySCustDemo()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $sales6 = DB::table('customer_list')
            ->select(DB::raw('UPPER(Race) AS Race'),DB::raw('COUNT(1) AS Num'))
            ->groupby(DB::raw('UPPER(Race)'))
            // ->orderby(DB::raw('CAST(Qty_Sold AS INTEGER)'), 'DESC')
            ->get();
          return view('summarySCustDemo',['wallpaper'=>$wallpaper],['sales6'=>$sales6]);
      }
      public function summarySTargetCustomer()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
        $sales7 = DB::table('total_sales_transaction')
            ->select('Customer_Name','INVC_NO','Rolling_Month','Qty_Sold','Orig_Price','Sales','Disc','Price','Orig_Tax',DB::raw('(Sales - Orig_Tax) AS before_gst'))
            ->groupby('Customer_Name')
            ->orderby('Customer_Name','ASC')
            ->take('20')
            ->get();
          return view('summarySTargetCustomer',['wallpaper'=>$wallpaper],['sales7'=>$sales7]);
      }
      public function summarySNumCust()
      {
        $wallpaper = DB::table('image')
        ->select('image.*')
        ->first();
          return view('summarySNumCust',['wallpaper'=>$wallpaper]);
      }
}
