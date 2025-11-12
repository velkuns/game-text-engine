# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

```yaml
## [tag] - YYYY-MM-DD
[tag]: https://github.com/eureka-framework/component-template/compare/1.0.0...master
### Changed
- Change 1
### Added
- Added 1
### Removed
- Remove 1
```



----

## [0.5.0] - 2025-11
[0.5.0]: https://github.com/velkuns/game-text-engine/compare/0.4.0...0.5.0
### Changed
- Rename Ability(ies) to Attribute(s)

----

## [0.4.0] - 2025-11
[0.4.0]: https://github.com/velkuns/game-text-engine/compare/0.3.0...0.4.0
### Added
- Add Rules part, as representation of rules files (Attributes, Statuses, Player & Combat)
- Add Leveling system
- Add auto leveling for bestiary to allow auto leveling of creatures when get them from bestiary
- Add Evaluator to evaluate rules
- Add RollResolver + EquippedWeaponItemResolver
- Add ValueResolverHandler to resolve specific value directly rather than get objet
### Changed
- Move all exception to the src/ root dir
- Update rules files to add leveling data and some other improvements
- Update PlayerApi to add player rules info
- Update CombatApi to add combat rules info
- Update AttributesApi & StatusesApi to add Rules part
- Re-organize code


----

## [0.3.0] - 2025-11
[0.3.0]: https://github.com/velkuns/game-text-engine/compare/0.2.1...0.3.0
### Changed
- Now conditions are under modifiers (not anymore on statuses)
- Rework resolver handler for type elements of conditions / modifiers
  - Add resolver by supported element type
- Rework condition validator
  - Add validator handler
  - Add validator by supported element type
- Rework modifier processors
  - Add modifier handler
  - Add modifier process by supported element type
- Update all tests
- Update all data / rules according to the changes
- Rework inventory from bestiary
  - Now have equipment par with item probability
  - Now have loot part for looting other items that is not equipment.
### Added
- Add TimeResolver::combatEnd() to clean statuses with duration at the end of combats
- Add some methods in elements classes according to the changes
- Add looting system

----

## [0.2.1] - 2025-10-30
[0.2.1]: https://github.com/velkuns/game-text-engine/compare/0.2.0...0.2.1
### Added
- Add ItemInterface::equip() method to equip an item
### Changed
- GameApi::read(): if source = target, do not apply the trigger (already be applied)
- Fix story test on condition for edge

## [0.2.0] - 2025-10-30
[0.2.0]: https://github.com/velkuns/game-text-engine/compare/0.1.0...master
### Added
- Add `isEnd` property on node when is node is end of graph
- Add GameApi::loadFromJsons() to load json directly
- Add GameApi::read() to handle read next step of story, with validation
- Add more tests
### Changed
- Remove GameApi properties "Api" suffix
- Update Readme

----

## [0.1.0] - 2025-10-29
[0.1.0]: https://github.com/velkuns/game-text-engine/compare/0.1.0...master
### Added
- Add Element system to manipulate and play
- Add Graph part to handle graph story
- Add API part to ease integration in clients
