<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';

/**
 * Abstract test case implementation for the PHP_Depend package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_InternalTokenizerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the tokenizer with a source file that contains only classes.
     *
     * @return void
     */
    public function testInternalTokenizerWithClasses()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/classes.php');
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $tokens = array(
            PHP_Depend_Code_Tokenizer::T_OPEN_TAG,
            PHP_Depend_Code_Tokenizer::T_DOC_COMMENT,
            PHP_Depend_Code_Tokenizer::T_ABSTRACT,
            PHP_Depend_Code_Tokenizer::T_CLASS,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CLASS,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_EXTENDS,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_PUBLIC,
            PHP_Depend_Code_Tokenizer::T_FUNCTION,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_DOUBLE_COLON,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_EQUAL,
            PHP_Depend_Code_Tokenizer::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_EQUAL,
            PHP_Depend_Code_Tokenizer::T_TRUE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        foreach ($tokens as $token) {
            $t = $tokenizer->next();
            $this->assertEquals($token, $t[0]);
        }
    }
 
    /**
     * Tests the tokenizer with a source file that contains mixed content of
     * classes and functions.
     *
     * @return void
     */   
    public function testInternalTokenizerWithMixedContent()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_class.php');
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $tokens = array(
            array(PHP_Depend_Code_Tokenizer::T_OPEN_TAG, 1),
            array(PHP_Depend_Code_Tokenizer::T_COMMENT, 2),
            array(PHP_Depend_Code_Tokenizer::T_FUNCTION, 5),
            array(PHP_Depend_Code_Tokenizer::T_STRING, 5),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN, 5),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, 5),
            array(PHP_Depend_Code_Tokenizer::T_COMMA, 5),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, 5),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE, 5),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN, 5),
            array(PHP_Depend_Code_Tokenizer::T_NEW, 6),
            array(PHP_Depend_Code_Tokenizer::T_STRING, 6),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN, 6),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, 6),
            array(PHP_Depend_Code_Tokenizer::T_COMMA, 6),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, 6),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE, 6),
            array(PHP_Depend_Code_Tokenizer::T_SEMICOLON, 6),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE, 7),
            array(PHP_Depend_Code_Tokenizer::T_DOC_COMMENT, 10),
            array(PHP_Depend_Code_Tokenizer::T_CLASS, 13),
            array(PHP_Depend_Code_Tokenizer::T_STRING, 13),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN, 13),
            array(PHP_Depend_Code_Tokenizer::T_COMMENT, 14),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE, 15),
            array(PHP_Depend_Code_Tokenizer::T_CLOSE_TAG, 16)
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        while (($token = $tokenizer->next()) !== PHP_Depend_Code_Tokenizer::T_EOF) {
            list($tok, $line) = array_shift($tokens);
            $this->assertEquals($tok, $token[0]);
            $this->assertEquals($line, $token[2]);
        }
    }
    
    /**
     * Tests that the tokenizer returns <b>T_BOF</b> if there is no previous
     * token.
     *
     * @return void
     */
    public function testInternalTokenizerReturnsBOFTokenForPrevCall()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_class.php');
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $this->assertEquals(PHP_Depend_Code_Tokenizer::T_BOF, $tokenizer->prev());
    }
    
    /**
     * Tests the tokenizer with a combination of procedural code and functions.
     *
     * @return void
     */
    public function testInternalTokenizerWithProceduralCodeAndFunction()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_code.php');
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $tokens = array(
            PHP_Depend_Code_Tokenizer::T_OPEN_TAG,
            PHP_Depend_Code_Tokenizer::T_FUNCTION,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_EQUAL,
            PHP_Depend_Code_Tokenizer::T_NEW,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_EQUAL,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_ARRAY,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_COMMA,
            PHP_Depend_Code_Tokenizer::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_CLOSE_TAG
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        while ($tokenizer->peek() !== PHP_Depend_Code_Tokenizer::T_EOF) {
            $token = $tokenizer->next();
            $this->assertEquals(array_shift($tokens), $token[0]);
        }
    }
    
    /**
     * Test case for undetected static method call added.
     *
     * @return void
     */
    public function testInternalTokenizerStaticCallBug01()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/01.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $tokens = array(
            PHP_Depend_Code_Tokenizer::T_OPEN_TAG,
            PHP_Depend_Code_Tokenizer::T_DOC_COMMENT,
            PHP_Depend_Code_Tokenizer::T_CLASS,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_PUBLIC,
            PHP_Depend_Code_Tokenizer::T_FUNCTION,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_DOUBLE_COLON,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,            
        );
        
        while (($token = $tokenizer->next()) !== PHP_Depend_Code_Tokenizer::T_EOF) {
            $this->assertEquals(array_shift($tokens), $token[0]);
        }
    }
    
    /**
     * Tests that the tokenizer handles the following syntax correct.
     * 
     * <code>
     * class Foo {
     *     public function formatBug09($x) {
     *         self::${$x};
     *     }
     * }
     * </code>
     * 
     * http://bugs.xplib.de/index.php?do=details&task_id=9&project=3
     * 
     * @return void
     */
    public function testInternalTokenizerDollarSyntaxBug09()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/09.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        
        $tokens = array(
            PHP_Depend_Code_Tokenizer::T_OPEN_TAG,
            PHP_Depend_Code_Tokenizer::T_DOC_COMMENT,
            PHP_Depend_Code_Tokenizer::T_CLASS,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_PUBLIC,
            PHP_Depend_Code_Tokenizer::T_FUNCTION,
            PHP_Depend_Code_Tokenizer::T_STRING,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_SELF, // SELF
            PHP_Depend_Code_Tokenizer::T_DOUBLE_COLON,
            PHP_Depend_Code_Tokenizer::T_DOLLAR,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN,
            PHP_Depend_Code_Tokenizer::T_VARIABLE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_SEMICOLON,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,
            PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE,            
        );
        
        while (($token = $tokenizer->next()) !== PHP_Depend_Code_Tokenizer::T_EOF) {
            $this->assertEquals(array_shift($tokens), $token[0]);
        }
    }
}