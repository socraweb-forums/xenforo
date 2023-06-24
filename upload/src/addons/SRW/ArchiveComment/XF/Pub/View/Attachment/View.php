<?php

namespace SRW\ArchiveComment\XF\Pub\View\Attachment;

use XF;
use XF\Http\ResponseFile;
use XF\Http\ResponseStream;
use XF\Util\File;
use ZipArchive;

class View extends XFCP_View
{
    public function renderRaw()
    {
        $raw = parent::renderRaw();

        if (!empty($this->params['return304']))
        {
            return $raw;
        }

        $attachment = $this->params['attachment'];

        if ($attachment->extension == ['zip', 'rar', 'tar', '7z', 'gz', 'bz2', 'xz', 'tgz', 'tar.gz', 'tar.bz2', 'tar.xz'])
        {
            $filePath = File::copyStreamToTempFile(XF::fs()->readStream($attachment->Data->getAbstractedDataPath()));

            $zip = new ZipArchive();

            $res = $zip->open($filePath);

            if ($res === true)
            {
                $zip->setArchiveComment(XF::options()->SRW_ac_comment_text);
                $zip->close();
            }

            if (!file_exists($filePath))
            {
                return $raw;
            }

            return $this->response->responseFile($filePath);
        }

        return $raw;
    }
}
