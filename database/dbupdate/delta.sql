ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD HAS_IMAGES DM_BOOL DEFAULT 0 NOT NULL;
ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD HAS_GWHA   DM_BOOL DEFAULT 0 NOT NULL;
ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD HAS_BOILER DM_BOOL DEFAULT 0 NOT NULL;
ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD HAS_CENTRAL_HEATING DM_BOOL DEFAULT 0 NOT NULL;
ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD HAS_WATER_COLUMN DM_BOOL DEFAULT 0 NOT NULL;
ALTER TABLE TBL_DOC_PROPOSAL_LIST ADD ROOMS_TYPE DM_SMALLINT DEFAULT  0 NOT NULL;
COMMIT WORK;

SET TERM #;
EXECUTE block returns (proposal_id integer, has_images integer)
AS
BEGIN
    for select t1.proposal_id from TBL_DOC_PROPOSAL_LIST as t1 into :proposal_id do
    begin
        has_images = 0;
        SELECT 1 FROM rdb$database WHERE EXISTS(SELECT fl.file_id FROM tbl_file_list fl WHERE fl.document_id = :proposal_id and fl.document_type = 'RealEstate_Document_RealEstateProposal') into :has_images;
        if (has_images > 0) then begin
         update TBL_DOC_PROPOSAL_LIST set has_images = :has_images where proposal_id = :proposal_id;
         suspend;
        end
    end
END#
SET TERM ;#
COMMIT WORK;

SET TERM ^;

CREATE TRIGGER TBL_FILE_LIST_AI FOR TBL_FILE_LIST
ACTIVE AFTER INSERT POSITION 0
AS
BEGIN
    IF(new.DOCUMENT_TYPE = 'RealEstate_Document_RealEstateProposal') then
    begin
        update TBL_DOC_PROPOSAL_LIST as pl set pl.has_images = 1 where pl.proposal_id = new.DOCUMENT_ID;
    end
END^

SET TERM ;^
COMMIT WORK;

SET TERM ^;
CREATE TRIGGER TBL_FILE_LIST_AD FOR TBL_FILE_LIST
ACTIVE AFTER DELETE POSITION 0
AS
BEGIN
    if (old.DOCUMENT_TYPE = 'RealEstate_Document_RealEstateProposal') then
    begin
        update TBL_DOC_PROPOSAL_LIST pl set pl.has_images = coalesce((SELECT 1 FROM rdb$database where exists(
        SELECT fl.file_id FROM tbl_file_list fl WHERE fl.document_id = OLD.DOCUMENT_ID and fl.document_type = OLD.DOCUMENT_TYPE)), 0) where pl.proposal_id = old.DOCUMENT_ID;
    end
END^

SET TERM ;^
COMMIT WORK;

DROP VIEW VW_DOC_PROPOSAL_LIST;
CREATE VIEW VW_DOC_PROPOSAL_LIST (PROPOSAL_ID, OWNER_PHONES, REALESTATETYPE_ID, REALESTATETYPE_TITLE, PROPOSAL_TYPE, OBJECT_ADDRESS, COMPLEX_AREA, PROPOSAL_DATE, PRICE, AGENCY_PRICE, CONTRAGENT_ID, CONTRAGENT_TITLE, DISTRICT_ID, DISTRICT_TITLE, SETTLEMENT_ID, SETTLEMENT_TITLE, STREET_ID, STREET_TITLE, PLANNINGTYPE_ID, PLANNINGTYPE_TITLE, REALTOR_ID, REALTOR_NAME, CONTRACT_CODE, IS_AGENCY, PROPOSALSTATUS_ID, PROPOSALSTATUS_TITLE, TOTAL_AREA, LIVING_AREA, KITCHEN_AREA, LASTCALLING_DATE, STOREY_NUMBER, STOREY_COUNT, OWNER_NAME, PROPOSAL_NOTICE, PURPOSE_ID, PURPOSE_TITLE, IS_PRIVATISATION, IS_GAS, IS_ELECTRICITY, IS_SEWERAGE, IS_WATER, VIEW_OF_SEA, CONTRACT_DATE, HEATINGTYPE_ID, HOTWATERSUPPLY_ID, EXCHANGE_NOTE, HAS_IMAGES)
AS
select pl.proposal_id,
       pl.owner_phones,
       pl.realestatetype_id,
       rtl.realestatetype_title,
       pl.proposal_type,
       pl.object_address,
       pl.total_area||'/'||pl.living_area||'/'||pl.kitchen_area,
       pl.proposal_date,
       pl.price,
       pl.agency_price,
       pl.contragent_id,
       cl.contragent_title,
       dl.district_id,
       dl.district_title,
       setl.settlement_id,
       setl.settlement_title,
       strl.street_id,
       strl.street_title,
       plt.planningtype_id,
       plt.planningtype_title,
       pl.realtor_id,
       (select fullname from proc_getfullusername(ul.user_firstname, ul.user_secondname, ul.user_lastname)) as realtor_name,
       pl.contract_code,
       iif(pl.contragent_id is not null, 1, 0) as is_agency,
       pl.proposalstatus_id,
       psl.proposalstatus_title,
       pl.total_area,
       pl.living_area,
       pl.kitchen_area,
       pl.lastcalling_date,
       pl.storey_number,
       pl.storey_count,
       pl.owner_name,
       pl.proposal_notice,
       pl.purpose_id,
       prl.purpose_title,
       pl.is_privatisation,
       pl.is_gas,
       pl.is_electricity,
       pl.is_sewerage,
       pl.is_water,
       pl.view_of_sea,
       pl.contract_date,
       pl.heatingtype_id,
       pl.hotwatersupply_id,
       pl.exchange_note,
       pl.HAS_IMAGES
from tbl_doc_proposal_list pl
left join tbl_dir_realestatetype_list rtl
    on rtl.realestatetype_id = pl.realestatetype_id
left join tbl_dir_contragent_list cl
    on cl.contragent_id = pl.contragent_id
left join tbl_dir_district_list dl
    on dl.district_id = pl.geodistrict_id and dl.DISTRICT_TYPE = 1
left join tbl_dir_settlement_list setl
    on setl.settlement_id = pl.settlement_id
left join tbl_dir_street_list strl
    on strl.street_id = pl.street_id
left join tbl_dir_planningtype_list plt
    on  plt.planningtype_id = pl.planningtype_id
left join tbl_sec_user_list ul
    on ul.user_id = pl.realtor_id
left join tbl_dir_proposalstatus_list  psl
    on psl.proposalstatus_id = pl.proposalstatus_id
left join tbl_dir_purpose_list prl
   on prl.purpose_id = pl.purpose_id;

GRANT DELETE, INSERT, REFERENCES, SELECT, UPDATE
 ON VW_DOC_PROPOSAL_LIST TO  SYSDBA WITH GRANT OPTION;

COMMIT WORK;