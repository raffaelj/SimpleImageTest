<!doctype html>
<html><head><title>SimpleImage Tests</title><link rel="stylesheet" href="style.css"></head><body>
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
    <tr><?php foreach ($info as $key => $val) { echo "<th>$key</th>\r\n"; } ?></tr>
    <tr><?php foreach ($info as $key => $val) { echo "<td>$val</td>\r\n"; } ?></tr>
</table>

<?php
/******************* SimpleImage Tests *******************/

require_once('SimpleImage.php');

// input and output dirs
$input = 'input';
$output = 'output';

// tests
$tests = [

//  ['method' => ['arg1', 'arg2']  ],
  
    ['crop'       => [10, 10, 40, 40]],
    ['resize'     => [40, 80]],
    
    ['bestFit'    => [40, 80]],
    ['thumbnail'  => [60, 60, 'center']],
    
    ['rotate'     => [10]],
    ['rotate'     => [45]],
    ['rotate'     => [90]],
    // ['rotate'     => [180]],
    ['rotate'     => [45, 'green']],
    
    ['sharpen'    => [10]],
    ['sketch'     => []],
    
];

/******************* Test Output *******************/

if (!is_dir($output))
    mkdir ($output, 0755);// create output dir

$error = '';

$dir = new DirectoryIterator($input);


// sort DirectoryIterator manually to iterate alphabetically
$sorted = [];
foreach ($dir as $file) {
    if ($file->isDot() || $file->isDir()) continue;
    $sorted[] = new \SPLFileObject($file->getPathname());
}
usort($sorted, function($a, $b){
    return strcmp($a->getFilename(), $b->getFilename());
});


// create images and html output
foreach ($sorted as $file) {

    echo "<div>";
    echo "<p><b>".$file->getFilename()."</b></p>";
    echo "<div><img src='".$file->getPathname()."' class='original'></div>";

    foreach ($tests as $test) {
      
        foreach ($test as $call => $args) {

            $outputFileName = "{$call}_" . implode('_', $args) . '_' . $file->getFilename();

            try {

                $img = new \claviska\SimpleImage($file->getPathname());

            } catch (\Exception $e) {

                // display the same error (unsopported file) only once
                echo $error === $e->getMessage() ? '' : '<div><span>' . $e->getMessage() . '</span></div>';
                $error = $e->getMessage();
                continue;

            }

            try {

                $img->$call(...$args) // splat args, requires PHP 5.6+
                    ->toFile("$output/$outputFileName");

                echo "<div><img src='$output/$outputFileName' alt='$outputFileName' title='$outputFileName'><span>$call</span></div>";

            } catch (\Exception $e) {

                echo '<div><span>' . $e->getMessage() . '</span></div>'; continue;

            }

        }
    }

    echo "</div>";

}

?>
</body></html>
