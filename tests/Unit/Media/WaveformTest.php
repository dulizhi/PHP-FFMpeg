<?php

namespace Tests\FFMpeg\Unit\Media;

use FFMpeg\Media\Waveform;

class WaveformTest extends AbstractMediaTestCase
{

    public function testFiltersReturnFilters()
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();

        $waveform = new Waveform($this->getAudioMock(__FILE__), $driver, $ffprobe, 640, 120);
        $this->assertInstanceOf(\FFMpeg\Filters\Waveform\WaveformFilters::class, $waveform->filters());
    }

    public function testAddFiltersAddsAFilter()
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();

        $filters = $this->getMockBuilder(\FFMpeg\Filters\FiltersCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filter = $this->getMockBuilder(\FFMpeg\Filters\Waveform\WaveformFilterInterface::class)->getMock();

        $filters->expects($this->once())
            ->method('add')
            ->with($filter);

        $waveform = new Waveform($this->getAudioMock(__FILE__), $driver, $ffprobe, 640, 120);
        $waveform->setFiltersCollection($filters);
        $waveform->addFilter($filter);
    }

    /**
     * @dataProvider provideSaveOptions
     */
    public function testSave(array $commands): void
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();

        $pathfile = '/tests/files/Audio.mp3';

        $commands[] = $pathfile;

        $driver->expects($this->once())
            ->method('command')
            ->with($commands);

        $waveform = new Waveform($this->getAudioMock(__FILE__), $driver, $ffprobe, 640, 120, ['#FFFFFF']);
        $this->assertSame($waveform, $waveform->save($pathfile));
    }

    public function provideSaveOptions()
    {
        return [
            [
                [
                    '-y', '-i', __FILE__, '-filter_complex',
                    'showwavespic=colors=#FFFFFF:s=640x120',
                    '-frames:v', '1',
                ],
            ],
        ];
    }
}
