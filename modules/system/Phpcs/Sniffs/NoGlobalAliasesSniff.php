<?php

namespace System\Console\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoGlobalAliasesSniff implements Sniff
{
    /**
     * Global aliases loaded from the aliases.php file.
     *
     * @var array
     */
    private $aliases = [];

    /**
     * Load aliases from the aliases.php file.
     */
    public function __construct()
    {
        $aliasesFile = __DIR__ . '/../../aliases.php';
        if (file_exists($aliasesFile)) {
            $this->aliases = include $aliasesFile;
        }
    }

    /**
     * Register tokens to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];
    }

    /**
     * Process the file for global alias usage.
     *
     * @param File $phpcsFile
     * @param int  $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Determine if the use statement is at the top of the file or within a class
        $isTopLevelUse = $this->isTopLevelUse($phpcsFile, $stackPtr);

        if (!$isTopLevelUse) {
            // This is a trait import, check if the alias is already imported
            $this->processTraitUse($phpcsFile, $stackPtr);
            return;
        }

        // Detect the full use statement
        $endOfStatement = $phpcsFile->findEndOfStatement($stackPtr);

        // Build the `use` statement without trailing semicolon
        $useStatement = '';
        for ($i = $stackPtr + 1; $i < $endOfStatement; $i++) {
            $useStatement .= $phpcsFile->getTokens()[$i]['content'];
        }

        // Normalize the use statement and remove "as" aliases
        $useStatement = trim(preg_replace('/\s+as\s+\w+$/i', '', $useStatement));

        // $phpcsFile->addError(
        //     $useStatement . var_export(isset($this->aliases[$useStatement]), true),
        //     $stackPtr,
        //     'NoGlobalAliases'
        // );

        // Check if the use statement matches a key in the aliases
        foreach ($this->aliases as $alias => $fullyQualifiedName) {
            if ($useStatement === $alias) {
                $fix = $phpcsFile->addFixableWarning(
                    "Avoid using global class alias '{$alias}'. Use '{$fullyQualifiedName}' instead.",
                    $stackPtr,
                    'NoGlobalAliases'
                );

                if ($fix) {
                    $phpcsFile->fixer->beginChangeset();

                    // Check if the original `use` statement includes an alias (e.g., `as $alias`)
                    $asPosition = $phpcsFile->findNext(T_AS, $stackPtr, $endOfStatement);
                    if ($asPosition !== false) {
                        // Retain the alias by capturing everything after "as"
                        $alias = $phpcsFile->getTokensAsString($asPosition, $endOfStatement - $asPosition);
                        $replacement = ' ' . $fullyQualifiedName . ' ' . trim($alias) . ';';
                    } else {
                        // No alias, replace with the fully qualified name directly
                        $replacement = ' ' . $fullyQualifiedName . ';';
                    }

                    // Replace the `use` statement
                    $phpcsFile->fixer->replaceToken($stackPtr + 1, $replacement);

                    // Remove any extra tokens between `use` and the end of the statement
                    for ($i = $stackPtr + 2; $i <= $endOfStatement; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }
        }
    }

    /**
     * Determine if the current use statement is at the top of the file.
     *
     * @param File $phpcsFile
     * @param int  $stackPtr
     * @return bool
     */
    private function isTopLevelUse(File $phpcsFile, int $stackPtr): bool
    {
        $prevClassToken = $phpcsFile->findPrevious([T_CLASS, T_TRAIT, T_INTERFACE], $stackPtr);
        return $prevClassToken === false;
    }

    /**
     * Process trait imports within a class.
     *
     * @param File $phpcsFile
     * @param int  $stackPtr
     */
    private function processTraitUse(File $phpcsFile, int $stackPtr)
    {
        $endOfStatement = $phpcsFile->findEndOfStatement($stackPtr);
        $traitUse = $phpcsFile->getTokensAsString($stackPtr + 1, $endOfStatement - $stackPtr);
        $traitUse = trim($traitUse);

        // Extract the short name of the trait
        $classParts = explode('\\', $traitUse);
        $alias = end($classParts);

        // Check if the alias matches a key in the aliases
        if (isset($this->aliases[$alias]) && !$this->isAlreadyImported($phpcsFile, $alias, $stackPtr)) {
            $phpcsFile->addWarning(
                "Trait '{$alias}' matches a global alias. Ensure it is explicitly imported at the top of the file.",
                $stackPtr,
                'TraitMatchesAlias'
            );
        }
    }

    /**
     * Check if a short alias has already been imported at the top of the file.
     *
     * @param File $phpcsFile
     * @param string $alias
     * @param int $stackPtr
     * @return bool
     */
    private function isAlreadyImported(File $phpcsFile, string $alias, int $stackPtr): bool
    {
        // Iterate over previous `use` statements at the top of the file
        $prevUse = $phpcsFile->findPrevious(T_USE, $stackPtr - 1);
        while ($prevUse !== false) {
            $endOfStatement = $phpcsFile->findEndOfStatement($prevUse);
            $useStatement = $phpcsFile->getTokensAsString($prevUse + 1, $endOfStatement - $prevUse);

            // Normalize and extract the alias
            $classParts = explode('\\', trim(preg_replace('/\s+as\s+\w+$/i', '', $useStatement)));
            $importedAlias = end($classParts);

            if ($importedAlias === $alias) {
                return true; // Found an existing import
            }

            $prevUse = $phpcsFile->findPrevious(T_USE, $prevUse - 1);
        }

        return false;
    }
}
