<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
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
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/PMD/Rule/Naming/ConstructorWithNameAsEnclosingClass.php';

/**
 * Test case for the constructor name rule.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Rule_Naming
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClassTest extends PHP_PMD_AbstractTest
{
    /**
     * testRuleAppliesToConstructorMethodNamedAsEnclosingClass
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToConstructorMethodNamedAsEnclosingClass()
    {
        $rule = new PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToConstructorMethodNamedAsEnclosingClassCaseInsensitive
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleAppliesToConstructorMethodNamedAsEnclosingClassCaseInsensitive()
    {
        $rule = new PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass();
        $rule->setReport($this->getReportMock(1));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToMethodNamedSimilarToEnclosingClass
     *
     * @return void
     * @covers PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass
     * @group phpmd
     * @group phpmd::rule
     * @group phpmd::rule::naming
     * @group unittest
     */
    public function testRuleNotAppliesToMethodNamedSimilarToEnclosingClass()
    {
        $rule = new PHP_PMD_Rule_Naming_ConstructorWithNameAsEnclosingClass();
        $rule->setReport($this->getReportMock(0));
        $rule->apply($this->getMethod());
    }
}