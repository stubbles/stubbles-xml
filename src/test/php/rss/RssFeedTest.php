<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\date\Date;

use function bovigo\assert\{
    assertThat,
    assertEmptyArray,
    assertEmptyString,
    assertFalse,
    assertNull,
    assertTrue,
    expect,
    predicate\equals,
    predicate\isSameAs
};
/**
 * Test for stubbles\xml\rss\RssFeedGenerator.
 */
#[Group('xml')]
#[Group('xml_rss')]
class RssFeedTest extends TestCase
{
    private RssFeed $rssFeed;

    protected function setUp(): void
    {
        $this->rssFeed = new RssFeed('test', 'http://stubbles.net/', 'description');
    }

    #[Test]
    public function hasGivenTitle(): void
    {
        assertThat($this->rssFeed->title(), equals('test'));
    }

    #[Test]
    public function hasGivenLink(): void
    {
        assertThat($this->rssFeed->link(), equals('http://stubbles.net/'));
    }

    #[Test]
    public function hasGivenDescription(): void
    {
        assertThat($this->rssFeed->description(), equals('description'));
    }

    #[Test]
    public function hasNoLocaleByDefault(): void
    {
        assertFalse($this->rssFeed->hasLocale());
    }

    #[Test]
    public function localeIsNullByDefault(): void
    {
        assertNull($this->rssFeed->locale());
    }

    #[Test]
    public function localeCanBeSet(): void
    {
        assertThat($this->rssFeed->setLocale('en_EN')->locale(), equals('en_EN'));
    }

    #[Test]
    public function hasNoCopyrightByDefault(): void
    {
        assertFalse($this->rssFeed->hasCopyright());
    }

    #[Test]
    public function copyrightIsNullByDefault(): void
    {
        assertNull($this->rssFeed->copyright());
    }

    #[Test]
    public function copyrightCanBeSet(): void
    {
        assertThat(
                $this->rssFeed->setCopyright('(c) 2012 Stubbles')->copyright(),
                equals('(c) 2012 Stubbles')
        );
    }

    #[Test]
    public function hasNoManagingEditorByDefault(): void
    {
        assertFalse($this->rssFeed->hasManagingEditor());
    }

    #[Test]
    public function managingEditorIsNullByDefault(): void
    {
        assertNull($this->rssFeed->managingEditor());
    }

    #[Test]
    public function managingEditorSetWithoutMailAddress(): void
    {
        assertThat(
                $this->rssFeed->setManagingEditor('mikey')->managingEditor(),
                equals('nospam@example.com (mikey)')
        );
    }

    #[Test]
    public function managingEditorSetWithMailAddress(): void
    {
        assertThat(
            $this->rssFeed->setManagingEditor('test@example.com (mikey)')
                ->managingEditor(),
            equals('test@example.com (mikey)')
        );
    }

    #[Test]
    public function hasNoStylesheetsByDefault(): void
    {
        assertEmptyArray($this->rssFeed->stylesheets());
    }

    #[Test]
    public function stylesheetsCanBeAdded(): void
    {
        assertThat(
            $this->rssFeed->appendStylesheet('foo.xsl')
                ->appendStylesheet('bar.xsl')
                ->stylesheets(),
            equals(['foo.xsl', 'bar.xsl'])
        );
    }

    #[Test]
    public function hasNoWebmasterByDefault(): void
    {
        assertFalse($this->rssFeed->hasWebMaster());
    }

    #[Test]
    public function webmasterEditorIsNullByDefault(): void
    {
        assertNull($this->rssFeed->webMaster());
    }

    #[Test]
    public function webmasterEditorSetWithoutMailAddress(): void
    {
        assertThat(
            $this->rssFeed->setWebMaster('mikey')->webMaster(),
            equals('nospam@example.com (mikey)')
        );
    }

    #[Test]
    public function webmasterEditorSetWithMailAddress(): void
    {
        assertThat(
            $this->rssFeed->setWebMaster('test@example.com (mikey)')
                ->webMaster(),
            equals('test@example.com (mikey)')
        );
    }

    #[Test]
    public function hasNoLastBuildDateByDefault(): void
    {
        assertFalse($this->rssFeed->hasLastBuildDate());
    }

    #[Test]
    public function initialLastBuildDateIsNull(): void
    {
        assertNull($this->rssFeed->lastBuildDate());
    }

    #[Test]
    public function lastBuildDateCanBePassedAsDateInstance(): void
    {
        $date = new Date('2008-05-24');
        assertThat(
            $this->rssFeed->setLastBuildDate($date)->lastBuildDate(),
            equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    #[Test]
    public function alternativeLastBuildDate(): void
    {
        $date = new Date('2008-05-24');
        assertThat(
            $this->rssFeed->setLastBuildDate('2008-05-24')->lastBuildDate(),
            equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    #[Test]
    public function settingInvalidLastBuildDateThrowsIllegalArgumentException(): void
    {
        expect(function() { $this->rssFeed->setLastBuildDate('foo'); })
            ->throws(\InvalidArgumentException::class);
    }

    #[Test]
    public function hasNoTimeToLiveByDefault(): void
    {
        assertFalse($this->rssFeed->hasTimeToLive());
    }

    #[Test]
    public function timeToLiveIsNullByDefault(): void
    {
        assertNull($this->rssFeed->timeToLive());
    }

    #[Test]
    public function timeToLiveCanBeSet(): void
    {
        assertThat($this->rssFeed->setTimeToLive(303)->timeToLive(), equals(303));
    }

    #[Test]
    public function hasNoImageByDefault(): void
    {
        assertFalse($this->rssFeed->hasImage());
    }

    #[Test]
    public function hasImageIfSet(): void
    {
        assertTrue(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description'
            )->hasImage()
        );
    }

    #[Test]
    public function imageUrlIsEmptyByDefault(): void
    {
        assertEmptyString($this->rssFeed->imageUrl());
    }

    #[Test]
    public function imageUrlCanBeSet(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description'
            )->imageUrl(),
            equals('http://example.com/foo.gif')
        );
    }

    #[Test]
    public function imageDescriptionIsEmptyByDefault(): void
    {
        assertEmptyString($this->rssFeed->imageDescription());
    }

    #[Test]
    public function imageDescriptionCanBeSet(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description'
            )->imageDescription(),
            equals('image description')
        );
    }

    #[Test]
    public function imageWidthIs88ByDefault(): void
    {
        assertThat($this->rssFeed->imageWidth(), equals(88));
    }

    #[Test]
    public function imageWidthIs88IfNotGiven(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description'
            )->imageWidth(),
            equals(88)
        );
    }

    #[Test]
    public function imageWidthCanBeSet(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description',
                100
            )->imageWidth(),
            equals(100)
        );
    }

    #[Test]
    public function imageHeightIs31ByDefault(): void
    {
        assertThat($this->rssFeed->imageHeight(), equals(31));
    }

    #[Test]
    public function imageHeightIs31IfNotGiven(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description'
            )->imageHeight(),
            equals(31)
        );
    }

    #[Test]
    public function imageHeightCanBeSet(): void
    {
        assertThat(
            $this->rssFeed->setImage(
                'http://example.com/foo.gif',
                'image description',
                100,
                150
            )->imageHeight(),
            equals(150)
        );
    }

    #[Test]
    public function imageWidthTooSmallThrowsIllegalArgumentException(): void
    {
        expect(function() {
            $this->rssFeed->setImage('http://example.org/example.gif', 'foo', -1);
        })->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function imageWidthTooGreatThrowsIllegalArgumentException(): void
    {
        expect(function() {
            $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 145);
        })->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function imageHeightTooSmallThrowsIllegalArgumentException(): void
    {
        expect(function() {
            $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, -1);
        })->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function imageHeightTooGreatThrowsIllegalArgumentException(): void
    {
        expect(function() {
            $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, 401);
        })->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function hasNoItemsByDefault(): void
    {
        assertThat($this->rssFeed->countItems(), equals(0));
        assertEmptyArray($this->rssFeed->items());
    }

    #[Test]
    public function retrieveNonExistingItemReturnsNull(): void
    {
        assertFalse($this->rssFeed->hasItem(0));
        assertNull($this->rssFeed->item(0));
    }

    #[Test]
    public function addedItemIsStored(): void
    {
        $item = $this->rssFeed->addItem('item', 'link', 'description');
        assertThat($this->rssFeed->countItems(), equals(1));
        assertThat($this->rssFeed->items(), equals([$item]));
        assertTrue($this->rssFeed->hasItem(0));
        assertThat($this->rssFeed->item(0), isSameAs($item));
    }
}
