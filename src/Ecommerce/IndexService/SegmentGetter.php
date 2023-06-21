<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace App\Ecommerce\IndexService;


use CustomerManagementFrameworkBundle\SegmentManager\SegmentManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;

class SegmentGetter implements GetterInterface
{
    const SEGMENT_GROUP_MANUFACTURER = 'Interest Manufacturer';
    const SEGMENT_GROUP_CAR_CLASS = 'Interest Car Class';
    const SEGMENT_GROUP_BODY_STYLE = 'Interest Body Style';

    /**
     * @var SegmentManagerInterface
     */
    protected $segmentManager;

    /**
     * SegmentGetter constructor.
     *
     * @param SegmentManagerInterface $segmentManager
     */
    public function __construct(SegmentManagerInterface $segmentManager)
    {
        $this->segmentManager = $segmentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($object, ?array $config = null): mixed
    {
        $segments = [];

        return $segments;
    }
}
