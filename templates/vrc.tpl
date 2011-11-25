#define runway 16777215
#define offset_threshold 8553090
#define apron 10447616
#define taxiway 10447616
#define taxi_center 4227200
#define hold_short 125
#define ils_hold_short 125
#define parking 100

[INFO]
xplane-data-tools-php sector output ; File description
<?= $this->icao; ?>_TWR ; Default Callsign
<?= $this->icao; ?> ; Default Airport
<?php list($lat, $lng) = explode(' ', Util::dec2Dms($this->geo)); ?>
<?= $lat; ?> ; Center lat 
<?= $lng; ?> ; Center lng
60 ; Lat compression
52 ; Lng compression
-2.2 ; Magnetic Variation
1.000000 ; Sector Scale Value (deprecated)

[AIRPORT]
<?= $this->icao; ?> <?=$this->freq_twr;?> <?= Util::dec2Dms($this->geo) ?> B ; <?= $this->name; ?> 

[RUNWAY]
; <?= $this->icao; ?> - <?= $this->name; ?> 
<?php foreach($this->runways AS $runway): ?>
<?= $runway['endpoint'][0]['number'] ?> <?= sprintf('%03d', $runway['endpoint'][1]['number']) ?> <?= sprintf('%03d', $runway['endpoint'][0]['bearing']); ?> <?= $runway['endpoint'][1]['bearing']; ?> <?= Util::dec2Dms($runway['endpoint'][0]['centerline_geo']) ?> <?= Util::dec2Dms($runway['endpoint'][1]['centerline_geo']) ?> 
<?php endforeach; ?>

[LABELS]
<?php foreach($this->runways AS $r): ?>
"<?= $r['endpoint'][0]['number'] ?>" <?= Util::dec2Dms($r['endpoint'][0]['sideline2_geo']); ?> ils_hold_short
"<?= $r['endpoint'][1]['number'] ?>" <?= Util::dec2Dms($r['endpoint'][1]['sideline2_geo']); ?> ils_hold_short
<?php endforeach; ?>

[GEO]
; ==========================================================
; # <?= $this->icao; ?> - <?= $this->name; ?> 
; ==========================================================

; ### RUNWAYS ### 
<?php foreach($this->runways AS $r): ?>
; Runway <?= $r['endpoint'][0]['number'] ?>/<?= $r['endpoint'][1]['number'] ?> 
<?= Util::dec2Dms($r['endpoint'][0]['centerline_geo']); ?> <?= Util::dec2Dms($r['endpoint'][1]['centerline_geo']); ?> runway  
<?= Util::dec2Dms($r['endpoint'][0]['sideline1_geo']); ?> <?= Util::dec2Dms($r['endpoint'][0]['sideline2_geo']); ?> runway 
<?= Util::dec2Dms($r['endpoint'][1]['sideline1_geo']); ?> <?= Util::dec2Dms($r['endpoint'][1]['sideline2_geo']); ?> runway 
<?= Util::dec2Dms($r['endpoint'][0]['sideline1_geo']); ?> <?= Util::dec2Dms($r['endpoint'][1]['sideline1_geo']); ?> runway 
<?= Util::dec2Dms($r['endpoint'][0]['sideline2_geo']); ?> <?= Util::dec2Dms($r['endpoint'][1]['sideline2_geo']); ?> runway 

<?php endforeach; ?>

; ### TAXIWAYS ### 
<?php foreach($this->taxiways AS $t): ?>
<?php $lastGeo = ''; ?>
; <?= $t['description'] ?>  
<?php foreach($t['nodes'] AS $node): ?>
<?php if($lastGeo != ''): ?>
<?= $lastGeo ?> <?= Util::dec2Dms($node['geo']) ?> taxiway
<?php endif; ?>
<? $lastGeo = Util::dec2Dms($node['geo']); ?>
<?php endforeach; ?>

<?php endforeach; ?>
; @TODO - Taxiway center lines?
N030.03.09.152 W090.01.46.815 N030.03.09.130 W090.01.45.805 taxi_center
;Apron borders
N030.02.30.595 W090.01.31.876 N030.02.31.670 W090.01.30.891 apron
;Runway 27 offset threshold
N030.03.07.732 W090.01.48.504 N030.03.07.751 W090.01.46.795 offset_threshold
;Holding points
N030.03.08.687 W090.01.45.818 N030.03.09.574 W090.01.45.791 hold_short
;Parkings
N030.02.28.338 W090.01.53.103 N030.02.28.648 W090.01.52.733 parking
;Helipads
N030.02.19.476 W090.01.31.464 N030.02.20.579 W090.01.31.393 parking