<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer\attributes;

use Attribute;
use stubbles\xml\serializer\delegate\Fragment;
use stubbles\xml\serializer\delegate\XmlSerializerDelegate;
use stubbles\xml\serializer\XmlSerializer;
use stubbles\xml\XmlStreamWriter;

/**
 * @since 10.1
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class XmlFragment extends Fragment
{

}