<?php



function returnSuccess($message = '', $code = 200)
{
    return response()->json([
        'err'  => false,
        'message' => $message,
        'errors' => (object)[],
        'data' => []
    ], $code);
}

function returnErrorMessage($message = '', $code = 200)
{
    return response()->json([
        'err'  => true,
        'message' => $message,
        'errors' => (object)[],
        'data' => []
    ], $code);
}
function returnData(array $data, $message = '', $code = 200)
{
    return response()->json([
        'err'  => false,
        'message' => $message,
        'errors' => (object)[],
        'data' => $data
    ], $code);
}

function saveimage($image,$path)
{
   $file = $image;
   $filename = $file->getClientOriginalName();
   $file->move($path,$filename);
   return $filename;
}

// function get($key)
// {
//     static $val;
//     if (empty($val)) {
//         $val = Setting::where('key', '=', $key)->first()->value;
//     }
//     return $val;
// }


// function user_Role($rule, $id)
// {

//     return ['game' => $game, 'services' => $services];
// }
