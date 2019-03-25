<?php
declare(strict_types=1);

namespace Sniffer\Hadouken\Sniffs\Metrics;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\NestingLevelSniff;

class HadoukenSniff extends NestingLevelSniff
{
    public $nestingLevel = 1;

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore abstract methods.
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$stackPtr]['scope_opener'];
        $end = $tokens[$stackPtr]['scope_closer'];

        $nestingLevel = 0;

        // Find the maximum nesting level of any token in the function.
        for ($i = ($start + 1); $i < $end; $i++) {
            $level = $tokens[$i]['level'];
            if ($nestingLevel < $level) {
                $nestingLevel = $level;
            }
        }

        // We subtract the nesting level of the function itself.
        $nestingLevel = ($nestingLevel - $tokens[$stackPtr]['level'] - 1);

        if ($nestingLevel > $this->absoluteNestingLevel) {
            $error = 'Function\'s nesting level (%s) exceeds allowed maximum of %s';
            $data = [
                $nestingLevel,
                $this->absoluteNestingLevel,
            ];
            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded', $data);
        } else {
            if ($nestingLevel > $this->nestingLevel) {
                $warning = '![D2e2-uLUwAAG5Yr](https://user-images.githubusercontent.com/7950487/54909662-10c5d000-4f2e-11e9-93cd-38274a6586bb.jpg)';
                $data = [
                    $nestingLevel,
                    $this->nestingLevel,
                ];
                $phpcsFile->addWarning($warning, $stackPtr, 'TooHigh', $data);
            }
        }
    }
}
