# AoC 2021

This year I finally had more time to push through all 50 tasks. Until day 21 I've done so without looking up on Reddit or other's people code, then the difficulty curve went a bit too steep for me.

### Some highlights from this year

#### Day 3 - Binary Diagnostic
As this task's input was in binary instead of parsing input lines as a string or array I've decided to stick to binary and just bitshift the numbers.

#### Day 5 - Hydrothermal Venture
First task with cartesian plane, first part was quite easy with only horizontal and vertical lines plotted on a diagram. Second part required diagonal lines where at first I was trying to start from figuring out the line slope that for 45 deg angle can either increase or decrease by 1 on each axis for each point.

#### Day 6 - Lanternfish
The moment I've read "exponentially" I knew it was going to be an optimization problem but went for quick and easy string array solution. Part 2 was counting fish in each state, shifting the array with each generation adding new fish to the right side as well as to 6th position.

#### Day 8 - Seven Segment Search
A very fun day, based on a signal to seven segment display the task was to guess what the displayed number it is not knowing what signal turns on what element of the display. It was a process of elimination based on how many signals are turned on in a given signal, most importantly each set contained all numbers from 0 to 9. Starting with top element of the display I could deduct it by comparing signal that is not present in 2-digit (1) and 3-digit (7) signals. In similar way and using already deducted signals I could figure out the rest of the display's mapping convert it to a "standard" mapping and guess the given numbers.

#### Day 9 - Smoke Basin
As usual first part was easy with loop checking each element on the grid. For the second part I went with approach to find all the low points first - because each area had exactly 1 low point - and the recursively fill all available spots until 9 or edge is found.

#### Day 12 - Passage Pathing
For this day I used recursive function that would check all possible routes. Second part allowed any small cave to be visited twice but only once so I had to keep track of small caves visited so far and when any small cave was visited the second time set a flag to remove any future small caves after first visit and remove any caves visited already once from future paths.

#### Day 13 - Transparent Origami
The "paper" is folded always in the middle of the card so to position all points after the fold I care only about points larger than fold value, for the rest I just need to subtract their value from paper's length before the fold to somewhat reverse their position.

#### Day 14 - Extended Polymerization
Another optimization task, very similar to lanternfish. As in day 6 I've done first part in a straightforward way using a very, very long string. For the second part the key was to realize that no matter what position a pair was in, it will create two no pairs in the next generation of polymer - and again in next generation I don't care what position of those pairs are just what 4 pairs in total they will generate. 

#### Day 15 - Chiton
It was all about finding path with Dijkstra, and as I'm not good with pathfinding at all it was off to YouTube to learn more. Instead of pure brute force my solution checks the shortest path to go to a given cell in the grid and uses it as "starting" point when checking adjacent tiles, when all cells are processed this way, the bottom right cell's value will be contain the shortest path to reach it. It is the least optimized day by far.

#### Day 16 - Packet Decoder
It took me a while to wrap my head around the mechanic of nested codes. The hardest part in my approach with cutting off the parsed portion of the code was detecting how long was the nested packet which resulted in a bit of duplicated code in "detect_packet_length" function.

#### Day 17 - Trick Shot
While solving part 1 I put the wrong input coordinates where my bigger y value was above zero, because of that during debugging I realized pretty obvious mechanic that projectile will always get back to y=0 at some point and all I need is to calculate from that.

#### Day 18 - Snailfish 
I rarely used array pointers, so I decided to focus on them during this day. Each string was parsed as an array, and it was recursively reduced based on task's rules. It was fairly straightforward with array pointers and because I've taken care of multi-digit numbers right from the start there was pretty much no debugging with the right input. The only downside is performance for part 2.

#### Day 19 - Beacon Scanner
This day took my the most time to complete, it was a fun problem to solve as it consisted of few steps that could be tackled one by one. First I had to find points that match between two scans, to do, so I calculated the distance between all the points in a single scan. Then based on identical length I could find matching pairs of points between scans, to find the two matching points in a given pair I summed all the offsets between points from both points on matching pairs and the connection between two points that occurred the most was the same point seen by two different scans.
The second step was about finding the right rotation translation between both scans, so if two scans had at least 12 matching overlapping points I've rotated the second array in one of 24 orienations and if after rotating all points did match it was the right one, if there were only 2 or 3 matching points the rotation could give false result.
Last step was about translating all the scans to the initial scan and counting unique points. The difficult part here was parsing all scans as a graph as scans only overlapped with some scans, to translate scan 5 I had to rotate it to be in line with scan 3, then rotate it again by scan 3 matrix. Here I had my biggest time loss as I thought I could only rotate the vector and then offest final scan by itm but I had to do both rotations and then final translation for the whole scan.
Part 2 with Manhattan distance was was a formality.

#### Day 20 - Trench Map
Like many others when I printed the image while debugging the script I've realized that the first input is # and not . like in the example and I guessed it has to be the key in this puzzle. The second clue was mention that the enchancment string could fit even a 9-digit binary number, so I've checked the last one and it was a dot. So the only difference I had to make in the script after an hour of thinking was to replace the part where I've set 0 to anything that was outside the initial picture to setting the result of the last binary.

#### Day 21 - Dirac Dice
Part 1 was a trivial loop, the second part was the first time I started over and went to Reddit for help. I figure out that as with lanternfish we don't need to track every game, just count amount of games that lead to given score in current generation. After I drew a 3 dice roll graph I realized the main shortcut for this problem, there are in total 27 different ways you can throw 3 side dice 3 times, but only 7 outcomes as we don't care about the order - 3 in total can only be result in a single dice combination but 6 can be a result of 7 different combinations.
But even after that I realized I will need to add that sum tens of millions of times and there has to be a better way, well there was but there was not. Method I figured out could work but to optimize it I would just need to cache the calculation. Each time the function run, it's result would be stored in an array based on what parameters were passed to that function. When it was run again it would check if function with identical parameters was run already and if so just returned the result. Well that day I learned what memoization is.

#### Day 22 - Reactor Reboot
Very interesting day but also very mind-bending for me. First part no problem just a 3d array with points processed directly. For the second part I needed to look up other's source to create a solution as I was stuck for a while when I calculated overlap between all cubes and based on it tried to parse the negative and positive changes in volume - but partially "cut" cuboid could not be compared by overlap to another one. The solution was to treat all overlaps as new cuboids and parse their volume with reverse signal. If two positive cuboids overlapped, to not count that overlapping area twice I saved new cuboid with a negative signal with coordinates based on where the two cuboids overlapped.

#### Day 23 - Amphipod
This was opposite day to day 19 solution, instead of figuring out step by step all necessary parts and slowly building up the solution it was very algorithm heavy. First part I figured out with pen and paper, with second part a tool made by one Redditor where I could enter my input and "play out" the game helped me to get part 2 answer pretty quickly. In the end I tried to follow other's solutions but PHP would not brute force some and other were to complex to deeply understand and implement without digging deep into the core algorithms and math that they used.

#### Day 24 - Arithmetic Logic Unit
There were some tasks in previous year that required input optimization "by hand" but not to this scale, it was a great task. I've put all the instructions to Excel and realized that they repeat 18 exact instructions 14 times. 14 was also the number of digits we had to guess, so I tried to figure out if each of those parts could be used to solve each number independently. Then without thinking more about the input I already tried to blindly brute force a solution which was a mistake. Again I went to reddit and read some great explanations on how each digit should be calculated using pairs of numbers that were result of the 5th instruction - either divided by 1 or 26, kept or shifted to the left. Part 2 had a single diference on how the numbers in each pair were complemented up or down.

#### Day 25 - Sea Cucumber
A relaxing jog to the finish line done without pretty much any debugging.

## Year 2021
This was the first year when I've completed all the tasks, and I've been doing them since 2017 with getting 20-24 stars at most each year, and I need to complete some previous years before next advent because I might have missed out on many great tasks. This year there were a lot of great early days like lanternfish or origami but the real fun started between days 15 and 20 wehere tasks were quite demanding but even without CS degree with enough time each one was doable. Days 21-24 were a true challenge that required not only clever thinking but also some standard knowledge about algorithms and optimization methods.
Nonetheless, it was a great year and most importantly I've learned a lot about deep ocean fauna and flora 😉