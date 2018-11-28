<!doctype html>
<html><head><title>SimpleImage Tests</title>
<style>
html{background:#eee;}
th, td {
    border-right: 1px solid #000;
    padding: .2em;
    text-align: center;
}
img{margin:.5em;vertical-align:middle;}
img.original{
    margin-right: 1em;
    padding: .5em;
    border: 1px solid #ccc;
}
div{border-bottom: 4px solid #ccc;}
</style></head><body>
<?php
// write some system info on top of page
$info = [
    'PHP_VERSION' => PHP_VERSION,
    'PHP_OS' => PHP_OS,
    'PHP_OS_FAMILY' => PHP_OS_FAMILY,
    'PHP_SAPI' => PHP_SAPI,
    'gd_info()["GD Version"]' => gd_info()['GD Version'],
    'GD_BUNDLED' => GD_BUNDLED,
    'GD_VERSION' => GD_VERSION,
];
?>
<table>
    <tr>
    <?php foreach ($info as $key => $val) {
        echo "<th>$key</th>\r\n";
    } ?>
    </tr>
    <tr>
    <?php foreach ($info as $key => $val) {
        echo "<td>$val</td>\r\n";
    } ?>
    </tr>
</table>

<?php
/******************* SimpleImage Tests *******************/

require_once('SimpleImage.php');

// input and output dirs
$input = 'input/';
$output = 'output/';

// tests
$tests = [

//  ['method' => ['arg1', 'arg2']  ],
  
    ['crop'   => [10, 10, 40, 40]],
    ['rotate' => [10]],
    ['rotate' => [45]],
    ['rotate' => [90]],
    ['rotate' => [180]],
    ['rotate' => [45, 'green']],
    ['resize' => [40, 80]],
    
    ['bestFit' => [40, 80]],
    ['thumbnail' => [60, 60, 'center']],
    
    ['sharpen' => [10]],
    ['sketch' => []],
    
];

if (!is_dir($output))
    mkdir ($output, 0755);// create output dir

$error = '';

$dir = new DirectoryIterator($input);

foreach ($dir as $file) {
  
    if ($file->isDot() || $file->isDir()) continue;
    
    echo "<div>";
    echo "<p><b>".$file->getFilename()."</b></p>";
    echo "<img src='".$file->getPathname()."' class='original'>";
    
    foreach ($tests as $test) {
        foreach ($test as $call => $args) {
            
            $outputFileName = "{$call}_" . implode('_', $args) . '_' . $file->getFilename();
            
            try {
              
                $img = new \claviska\SimpleImage($file->getPathname());
                
            } catch (\Exception $e) {
              
                echo $error === $e->getMessage() ? '' : '<span>' . $e->getMessage() . '</span>';
                $error = $e->getMessage();
                continue;
                
            }
            
            try {
              
                $img->$call(...$args) // splat args, requires PHP 5.6+
                    ->toFile($output.$outputFileName);
                
                echo "<img src='$output/$outputFileName' alt='$outputFileName' title='$outputFileName'>";
                
            } catch (\Exception $e) {
              
                echo '<span>' . $e->getMessage() . '</span>'; continue;
                
            }
            
        }
    }
    
    echo "</div>";
    
}


?>
</body></html>
