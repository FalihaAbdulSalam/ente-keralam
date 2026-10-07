<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContestPoint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ContestController extends Controller
{
    private $contests = [
        [
            'contest_id' => 1,
            'slug' => 'reel-contest-1',
            'contest_name' => 'Reel Contest-വിഷയം-കേരളത്തിലെ ഐ ടി രംഗത്തെ കുതിച്ചുചാട്ടം',
            'title' => 'കേരളത്തിലെ ഐ ടി രംഗത്തെ കുതിച്ചുചാട്ടം',
            'description' => 'കേരളത്തിൽ  ഐ ടി രം ഗത്ത് വലിയ കുതിച്ചുചാട്ടമാണ് ഉണ്ടായിരിക്കുന്നത്. ഐ ടി കയറ്റുമതിയിലെ വർദ്ധന, ഉയർന്ന ജജോലി സാധ്യതകൾ, മെച്ചപ്പെട്ട  അടിസ്ഥാന സൗകര്യങ്ങൾ, സ്റ്റാർട്ടപ്പ് മിഷന്റെ പ്രവർത്തനങ്ങൾ  അങ്ങനെ നെ ഒട്ടനവധി ഘടകങ്ങൾ ഐ ടി മേഖലയുടെ  വളർച്ചയ്ക്ക് കരുത്തേകിയിട്ടുണ്ട് . നിങ്ങൾക്ക് അടുത്തറിയാനായ പുരോഗതി  എന്തൊക്കെയാണ്? അത്  റീലായി ചിത്രീകരിച്ച് സമർപ്പിക്കൂ.',
            'type' => 'Reel',
            'banner' => '/uploads/contest/banners/reel1_banner.webp',
            'poster' => '/uploads/contest/posters/reel1_poster.jpg',
            'video_path' => '/reel1_video_light/reel1.mp4',
            'score' => 100,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ],
        [
            'contest_id' => 2,
            'slug' => 'reel-contest-2',
            'contest_name' => 'Reel Contest-വിഷയം -അന്നും ഇന്നും: എൻ്റെ  നാടിന് 10 വർഷത്തിലുണ്ടായ പുരോഗതി',
            'title' => 'അന്നും ഇന്നും: എൻ്റെ  നാടിന് 10 വർഷത്തിലുണ്ടായ പുരോഗതി',
            'description' => "നിങ്ങളുടെ  നാടിന് പത്ത് വർഷം കൊണ്ടുണ്ടായ പുരോഗതി\r\nറോഡുകൾക്  വന്ന പുരോഗതി, നാട്ടിലുണ്ടായ വികസനങ്ങൾ, സ്കൂളുകൾക്കുണ്ടായ\r\nപുരോഗതി. ശ്രദ്ധയിൽ  പെട്ടവ രസകരമായ റീലുകളായി അയക്കൂ.",
            'type' => 'Reel',
            'banner' => '/uploads/contest/banners/reel2_banner.webp',
            'poster' => '/uploads/contest/posters/reel2_poster.jpg',
            'video_path' => '/reel2_video_light/reel2.mp4',
            'score' => 100,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ],
        [
            'contest_id' => 3,
            'slug' => 'photo-contest-1',
            'contest_name' => 'Photo Contest-വിഷയം -പുഞ്ചിരി നിറയുന്ന കേരളം.',
            'title' => 'Photo Contest',
            'description' => 'പുഞ്ചിരി നിറയുന്ന കേരളം.',
            'type' => 'Photo',
            'banner' => '/uploads/contest/banners/photo_contest_banner.webp',
            'poster' => '/uploads/contest/posters/photo_contest_poster.jpg',
            'score' => 100,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ],
        [
            'contest_id' => 4,
            'slug' => 'essay-contest-1',
            'contest_name' => 'Essay Writing Contest-വിഷയം -2031 ലെ എൻ്റെ കേരളം.',
            'title' => 'Essay Writing Contest',
            'description' => '2031 ലെ എൻ്റെ കേരളം.',
            'type' => 'Essay',
            'banner' => '/uploads/contest/banners/essay_contest_banner.webp',
            'poster' => '/uploads/contest/posters/essay_contest_poster.jpg',
            'score' => 75,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ],
        [
            'contest_id' => 5,
            'slug' => 'poem-contest-1',
            'title' => 'Poem Writing Contest',
            'contest_name' => 'Poem Writing Contest-വിഷയം -മുന്നേറിയ  കേരളം.',
            'description' => 'മുന്നേറിയ  കേരളം.',
            'type' => 'Poem',
            'banner' => '/uploads/contest/banners/poem_contest_banner.webp',
            'poster' => '/uploads/contest/posters/poem_contest_poster.jpg',
            'score' => 75,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ],
           [
            'contest_id' => 6,
            'slug' => 'kerala-development-video-contest',
            'contest_name' => 'Video Contest-വിഷയം-കേരളത്തിലെ 10 വർഷത്തെ വികസന നേട്ടങ്ങൾ',
            'title' => 'കേരളത്തിലെ 10 വർഷത്തെ വികസന നേട്ടങ്ങൾ',
            'description' => 'തമ്പ് സ്റ്റോപ്പേഴ്‌സ് (Thumb-stoppers) നിർമ്മിക്കാൻ അവസരം (ഫേസിങ് പേജ്)

ഫേസ്ബുക്ക്, ഇൻസ്റ്റാഗ്രാം, യൂട്യൂബ് എന്നിവയിലെ ഉള്ളടക്കങ്ങളുടെ സ്ക്രോളിംഗിനിടെ പെട്ടെന്ന് ശ്രദ്ധ കിട്ടുന്ന തരത്തിൽ തമ്പ് സ്റ്റോപ്പേഴ്‌സ് നിർമ്മിക്കാൻ നിങ്ങൾക്ക് കഴിയുമോ ?

എങ്കിൽ കേരളത്തിലെ 10 വർഷത്തെ വികസന നേട്ടങ്ങൾ അടയാളപ്പെടുത്തുന്ന തരത്തിൽ ഒരു മിനിറ്റ് ദൈർഘ്യമുള്ള ഒരു വീഡിയോ ചിത്രീകരിച്ച് ഈ വെബ് സൈറ്റിൽ അപ്ലോഡ് ചെയ്യൂ .ഒപ്പം തമ്പ് സ്റ്റോപ്പേഴ്‌സിനായി ആശയങ്ങളും നിർദ്ദേശിക്കാം .ആശയങ്ങൾ prdprogrammeproduction@gmail.com ലേക്ക്‌ വേണം  ഇ-മെയിൽ ചെയ്യേണ്ടത് .തെരഞ്ഞെടുക്കുന്ന ആശയങ്ങൾ ഉപയോഗിച്ച് നിങ്ങൾക്ക്  തന്നെ പിന്നീട് വീഡിയോ നിർമ്മിക്കാം.

ഇൻഫർമേഷൻ പബ്ലിക് റിലേഷൻസ് വകുപ്പാണ് നവാഗതരായ പ്രതിഭകൾക്കായി ഈ അവസരം ഒരുക്കുന്നത്.
വേഗം നിങ്ങളുടെ ആശയങ്ങൾ prdprogrammeproduction@gmail.com ലേക്കും വീഡിയോകൾ ഈ വെബ്സൈറ്റിലേക്കും അപ്ലോഡ് ചെയ്യൂ .അവസാന തീയതി ഡിസംബർ 31.',
            'type' => 'Video',
            'add_info'=>'
1.10 വർഷത്തെ കേരളത്തിൻ്റെ വികസന നേട്ടങ്ങളാവണം വിഷയമാക്കേണ്ടത്.

2.പ്രതിസന്ധികളിൽ തളരാത്ത കേരളം ,കേരളം നമ്പർ 1 ,സ്മാർട്ട് കേരളം ,മുന്നേറുന്ന നാട്  ,തുടരണം ഈ വികസനം ,സാമൂഹിക സുരക്ഷയൊരുക്കുന്ന കേരളം ,തുല്യത ഉറപ്പാക്കുന്ന സംസ്ഥാനം ,ക്രമാസമാധാനമുള്ള നാട്,മികവുറ്റ റോഡുകൾ,അതിദാരിദ്യ്രമില്ലാത്ത നാട് തുടങ്ങി ഏതു വികസന വിഷയങ്ങളിലും നിങ്ങൾക്ക് വീഡിയോ ചിത്രീകരിക്കാം .തമ്പ് സ്റ്റോപ്പേഴ്‌സിനായി ആശയങ്ങളും സമർപ്പിക്കാം.

3 .ഒരാൾക്ക് ഒരു വീഡിയോയും 5 ആശയങ്ങളും വരെ സമർപ്പിക്കാം .മൊബൈലിലോ ,വീഡിയോ ക്യാമറയിലോ വീഡിയോകൾ ചിത്രീകരിക്കാം .വെർട്ടിക്കൽ ഫോർമാറ്റിലാണ് വീഡിയോകൾ നിർമ്മിക്കേണ്ടത്.
4 .വീഡിയോ  mp4 ഫോർമാറ്റിൽ  ആയിരിക്കണം  അപ്‌ലോഡ്  ചെയ്യേണ്ടത് .പരമാവധി  size 500 Mb .

5 .അപേക്ഷകർ 50 വയസിൽ താഴെ പ്രായമുള്ളവരാകണം 

6 .തമ്പ് സ്റ്റോപ്പേഴ്‌സിനായി തെരഞ്ഞെടുക്കപ്പെടുന്ന ആശയങ്ങൾ ഓരോന്നും ചിത്രീകരിക്കാൻ പരമാവധി മൂന്ന് ലക്ഷം രൂപ വരെയാണ് നൽകുക. 

7 .വീഡിയോകളുടെ കോപ്പി റൈറ്റ് അവകാശം സംസ്ഥാന സർക്കാരിനായിരിക്കും. 

8 .സർക്കാരിന്റെ വികസന നേട്ടങ്ങൾ സംബന്ധിച്ച്  കുറഞ്ഞ വാക്കുകളിലുള്ള വിവരങ്ങൾ ഈ വെബ് സൈറ്റിൽ പ്രസിദ്ധീകരിച്ചിട്ടുണ്ട്.',
            'banner' => '/uploads/contest/banners/video_banner.jpeg',
            'poster' => '/uploads/contest/posters/video_poster.jpg',
            'subject'=>'സർക്കാരിന്റെ 10 വർഷത്തെ വികസന പ്രവർത്തനങ്ങൾ ഒറ്റ നോട്ടത്തിൽ',
            'sub_file' => '/uploads/contest/THUMB_STOPPERS.pdf',
            'instructions_text' => 'പങ്കെടുക്കുന്നതിന് മുമ്പായി നിർദ്ദേശങ്ങൾ ദയവായി വായിക്കുക അല്ലെങ്കിൽ ഡൗൺലോഡ് ചെയ്യുക',
            'instructions' => '/uploads/contest/instructions.pdf',
            'video_path' => '/reel1_video_light/reel1.mp4',
            'score' => 100,
            'start_date' => '2025-01-01',
            'end_date' => '2027-12-31',
            'status' => 1
        ]
    ];

    // Helper method to add full storage URLs to contest data
    private function formatContestUrls($contest)
    {
        $contest = $this->applyContestPoints($contest);

        return array_merge($contest, [
            'banner' => Storage::url($contest['banner']),
            'poster' => Storage::url($contest['poster']),
            'video_path' => isset($contest['video_path']) ? Storage::url($contest['video_path']) : null,
            'sub_file' => isset($contest['sub_file']) ? Storage::url($contest['sub_file']) : null,
            'instructions' => isset($contest['instructions']) ? Storage::url($contest['instructions']) : null,
        ]);
    }

    private function applyContestPoints(array $contest): array
    {
        if (!Schema::hasTable('contest_points')) {
            return $contest;
        }

        $points = ContestPoint::where('contest_id', $contest['contest_id'])->first();
        if (!$points) {
            return $contest;
        }

        $contest['score'] = (int) $points->participation_points;
        $contest['bonus_first'] = (int) $points->bonus_first;
        $contest['bonus_second'] = (int) $points->bonus_second;
        $contest['bonus_third'] = (int) $points->bonus_third;

        return $contest;
    }

    // Get all contests
    public function getContests()
    {
        $activeContests = collect($this->contests)
            ->where('status', 1)
            ->map(fn($contest) => $this->formatContestUrls($contest))
            ->values()
            ->all();
        
        return response()->json([
            'result' => true,
            'message' => 'Contests retrieved successfully.',
            'data' => $activeContests
        ]);
    }

    // Get individual contest by ID
    public function getContestById($id)
    {
        $contest = collect($this->contests)->firstWhere('contest_id', (int) $id);

        if (!$contest) {
            return response()->json([
                'result' => false,
                'message' => 'Contest not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Contest retrieved successfully.',
            'data' => $this->formatContestUrls($contest)
        ]);
    }

    // Get individual contest by slug
    public function getContestBySlug($slug)
    {
        $contest = collect($this->contests)->firstWhere('slug', $slug);

        if (!$contest) {
            return response()->json([
                'result' => false,
                'message' => 'Contest not found.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Contest retrieved successfully.',
            'data' => $this->formatContestUrls($contest)
        ]);
    }
}
