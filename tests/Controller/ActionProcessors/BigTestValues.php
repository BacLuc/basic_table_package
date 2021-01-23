<?php

namespace BaclucC5Crud\Test\Controller\ActionProcessors;

class BigTestValues {
    const WYSIWYGVALUE = <<<'HEREDOC'
<p>test</p>

<p>&nbsp;</p>

<p>test2</p>

<p>&nbsp;</p>

<p><strong>asdfasdf</strong></p>

<p>&nbsp;</p>

<p><u><a href="http://google.com"><strong>asdfasdf</strong></a></u></p>
HEREDOC;

    const WYSIWYGVALUE2 = <<<'HEREDOC'
<p>test2</p>

<p>&nbsp;</p>

<p>test2</p>

<p>&nbsp;</p>

<p><strong>asdfasdf</strong></p>

<p>&nbsp;</p>

<p><u><a href="http://google.com"><strong>asdfasdf</strong></a></u></p>
HEREDOC;
}
