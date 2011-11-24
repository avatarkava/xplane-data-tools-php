README
======

What is x-plane-data-tools-php?
-------------------------------
x-plane-data-tools-php is a toolkit designed to parse and convert free airport and navigation data provided by Laminar Research under the Free Software Foundation General Public License (GPL) into a variety of formats that could be useful in flight simulation or virtual air traffic control on various networks.  

More information on X-Plane is available at http://www.x-plane.com.
If you would like to download a copy of the AIRAC (about 90MB uncompressed), it can be found at http://data.x-plane.com.

Current functionality is limited to:
- splitting the large apt.dat file into one file per airport to ease processing/conversion requirements
- generating a .sct2 compatible sector file component for VRC terminal client including runways, taxiways, parking areas, aprons, etc.

It is very much an early work in progress.  and there are lots of improvements that I can think of but just don't have the time for at the moment, including:
- Conversions to .es format for EuroScope
- Inclusion of runway numbers, taxiway names and centerlines, terminal building markers on VRC export
- Some smoothing to limit size of the resultant file / discarding data not critical to the diagrams

If you have questions, feel free to ask!

Mike Burke <mburke@dmwtechnology.com>

Licensing
------------
Check the LICENSE file included with this package.

Requirements
------------
- PHP 5.0 and up. More requirements will be documented as they're figured out.
- X-Plane apt.dat airport data file available at http://data.x-plane.com.

Installation
------------
This program is currently designed to be run from the command line (CLI).  Download the toolkit and place a copy of apt.dat from the x-plane data site in the root directory (see above).

Documentation
-------------
Check the docs folder.

Contributing
------------
This is a completely open-source community project.  Contribute if you'd like!  
